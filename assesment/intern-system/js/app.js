    const App = {
            init() {
                this.setupNavigation();
                this.setupForms();
                this.setupFilters();
                Renderer.renderDashboard();
                Renderer.renderInterns();
                Renderer.renderTasks();
                Renderer.renderLogs();
            },

            setupNavigation() {
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        const view = e.currentTarget.dataset.view;
                        this.navigateTo(view);
                    });
                });
            },

            navigateTo(view) {
                document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                
                document.getElementById(view).classList.add('active');
                document.querySelector(`[data-view="${view}"]`).classList.add('active');
                
                AppState.currentView = view;
            },

            setupForms() {
                document.getElementById('intern-form').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.handleInternSubmit(e.target);
                });

                document.getElementById('task-form').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.handleTaskSubmit(e.target);
                });
            },

            setupFilters() {
                document.getElementById('status-filter').addEventListener('change', (e) => {
                    AppState.filters.status = e.target.value;
                    Renderer.renderInterns();
                });

                document.getElementById('skill-filter').addEventListener('change', (e) => {
                    AppState.filters.skill = e.target.value;
                    Renderer.renderInterns();
                });
            },

            async handleInternSubmit(form) {
                const formData = new FormData(form);
                const data = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    skills: formData.getAll('skills')
                };

                const errors = Validators.validateIntern(data);
                if (errors.length > 0) {
                    Renderer.showAlert(errors.join(', '), 'error');
                    return;
                }

                Renderer.showLoading();
                const isUnique = await FakeServer.checkEmailUnique(data.email);
                
                if (!isUnique) {
                    Renderer.hideLoading();
                    Renderer.showAlert('Email already exists in the system', 'error');
                    return;
                }

                const year = new Date().getFullYear();
                const intern = {
                    id: `${year}-${String(AppState.nextInternId).padStart(3, '0')}`,
                    ...data,
                    status: 'ONBOARDING',
                    createdAt: new Date().toISOString()
                };

                AppState.interns.push(intern);
                AppState.nextInternId++;
                
                this.addLog('INTERN_CREATED', `New intern added: ${intern.name} (${intern.id})`);
                
                Renderer.hideLoading();
                Renderer.showAlert(`Intern ${intern.name} created successfully!`);
                Renderer.renderDashboard();
                Renderer.renderInterns();
                Renderer.renderTasks();
                form.reset();
            },

            async handleTaskSubmit(form) {
                const formData = new FormData(form);
                const data = {
                    title: formData.get('title'),
                    description: formData.get('description'),
                    estimatedHours: parseInt(formData.get('estimatedHours')),
                    requiredSkills: formData.getAll('requiredSkills'),
                    assignedTo: formData.get('assignedTo') || null,
                    dependencies: Array.from(document.getElementById('dependencies-select').selectedOptions).map(o => o.value)
                };

                const errors = Validators.validateTask(data);
                if (errors.length > 0) {
                    Renderer.showAlert(errors.join(', '), 'error');
                    return;
                }

                const taskId = `TASK-${String(AppState.nextTaskId).padStart(3, '0')}`;
                
                if (data.dependencies?.length) {
                    const tempTasks = [...AppState.tasks, { id: taskId, dependencies: data.dependencies }];
                    if (RulesEngine.detectCircularDependency(taskId, data.dependencies, tempTasks)) {
                        Renderer.showAlert('Circular dependency detected! Please revise dependencies.', 'error');
                        return;
                    }
                }

                if (data.assignedTo) {
                    const intern = AppState.interns.find(i => i.id === data.assignedTo);
                    const taskObj = { requiredSkills: data.requiredSkills };
                    if (!RulesEngine.canAssignTask(intern, taskObj)) {
                        Renderer.showAlert('Cannot assign: Intern lacks required skills or is not active', 'error');
                        return;
                    }
                }

                const task = {
                    id: taskId,
                    ...data,
                    status: 'PENDING',
                    createdAt: new Date().toISOString()
                };

                AppState.tasks.push(task);
                AppState.nextTaskId++;
                
                this.addLog('TASK_CREATED', `New task created: ${task.title} (${task.id})`);
                
                Renderer.showAlert(`Task "${task.title}" created successfully!`);
                Renderer.renderDashboard();
                Renderer.renderTasks();
                form.reset();
            },

            activateIntern(internId) {
                const intern = AppState.interns.find(i => i.id === internId);
                if (!intern) return;

                if (!RulesEngine.canTransitionStatus(intern.status, 'ACTIVE')) {
                    Renderer.showAlert('Invalid status transition', 'error');
                    return;
                }

                intern.status = 'ACTIVE';
                this.addLog('STATUS_CHANGED', `${intern.name} transitioned to ACTIVE`);
                Renderer.showAlert(`${intern.name} is now ACTIVE!`);
                Renderer.renderDashboard();
                Renderer.renderInterns();
            },

            exitIntern(internId) {
                const intern = AppState.interns.find(i => i.id === internId);
                if (!intern) return;

                if (!RulesEngine.canTransitionStatus(intern.status, 'EXITED')) {
                    Renderer.showAlert('Invalid status transition', 'error');
                    return;
                }

                intern.status = 'EXITED';
                this.addLog('STATUS_CHANGED', `${intern.name} has EXITED`);
                Renderer.showAlert(`${intern.name} has exited the system`);
                Renderer.renderDashboard();
                Renderer.renderInterns();
            },

            startTask(taskId) {
                const task = AppState.tasks.find(t => t.id === taskId);
                if (!task) return;

                task.status = 'IN_PROGRESS';
                this.addLog('TASK_STARTED', `Task started: ${task.title}`);
                Renderer.showAlert(`Task "${task.title}" started!`);
                Renderer.renderTasks();
                Renderer.renderDashboard();
            },

            completeTask(taskId) {
                const task = AppState.tasks.find(t => t.id === taskId);
                if (!task) return;

                if (!RulesEngine.canCompleteTask(task, AppState.tasks)) {
                    Renderer.showAlert('Cannot complete: Dependencies are not finished', 'error');
                    return;
                }

                task.status = 'DONE';
                task.completedAt = new Date().toISOString();
                this.addLog('TASK_COMPLETED', `Task completed: ${task.title}`);
                
                this.checkDependentTasks(taskId);
                
                Renderer.showAlert(`Task "${task.title}" completed!`);
                Renderer.renderDashboard();
                Renderer.renderTasks();
            },

            checkDependentTasks(completedTaskId) {
                AppState.tasks.forEach(task => {
                    if (task.dependencies?.includes(completedTaskId)) {
                        if (task.status === 'PENDING' && RulesEngine.canCompleteTask(task, AppState.tasks)) {
                            this.addLog('DEPENDENCY_RESOLVED', `All dependencies resolved for: ${task.title}`);
                        }
                    }
                });
            },

            addLog(action, details) {
                AppState.logs.push({
                    timestamp: new Date().toISOString(),
                    action,
                    details
                });
                Renderer.renderLogs();
                Renderer.renderDashboard();
            }
        };

        // Initialize Application
        document.addEventListener('DOMContentLoaded', () => {
            App.init();
        });