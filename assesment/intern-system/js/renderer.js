 const Renderer = {
            showAlert(message, type = 'success') {
                const container = document.getElementById('alert-container');
                const icon = type === 'success' ? '✓' : '✕';
                container.innerHTML = `
                    <div class="alert alert-${type}">
                        <span class="alert-icon">${icon}</span>
                        <span>${message}</span>
                    </div>
                `;
                setTimeout(() => container.innerHTML = '', 5000);
            },

            showLoading() {
                const container = document.getElementById('alert-container');
                container.innerHTML = `
                    <div class="loading-overlay">
                        <div class="spinner"></div>
                        <div class="loading-text">Processing request...</div>
                    </div>
                `;
            },

            hideLoading() {
                document.getElementById('alert-container').innerHTML = '';
            },

            renderDashboard() {
                const stats = {
                    totalInterns: AppState.interns.length,
                    activeInterns: AppState.interns.filter(i => i.status === 'ACTIVE').length,
                    onboardingInterns: AppState.interns.filter(i => i.status === 'ONBOARDING').length,
                    totalTasks: AppState.tasks.length,
                    completedTasks: AppState.tasks.filter(t => t.status === 'DONE').length,
                    inProgressTasks: AppState.tasks.filter(t => t.status === 'IN_PROGRESS').length,
                    totalHours: AppState.tasks.reduce((sum, t) => sum + t.estimatedHours, 0)
                };

                document.getElementById('dashboard-stats').innerHTML = `
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Total Interns</div>
                            <div class="stat-card-icon">👥</div>
                        </div>
                        <div class="stat-card-value">${stats.totalInterns}</div>
                        <div class="stat-card-subtitle">${stats.activeInterns} active, ${stats.onboardingInterns} onboarding</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Total Tasks</div>
                            <div class="stat-card-icon">✅</div>
                        </div>
                        <div class="stat-card-value">${stats.totalTasks}</div>
                        <div class="stat-card-subtitle">${stats.completedTasks} completed, ${stats.inProgressTasks} in progress</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Total Hours</div>
                            <div class="stat-card-icon">⏱️</div>
                        </div>
                        <div class="stat-card-value">${stats.totalHours}</div>
                        <div class="stat-card-subtitle">Estimated work hours</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Completion Rate</div>
                            <div class="stat-card-icon">📈</div>
                        </div>
                        <div class="stat-card-value">${stats.totalTasks ? Math.round((stats.completedTasks / stats.totalTasks) * 100) : 0}%</div>
                        <div class="stat-card-subtitle">Tasks completed</div>
                    </div>
                `;

                const recentLogs = AppState.logs.slice(-5).reverse();
                document.getElementById('dashboard-logs').innerHTML = recentLogs.length > 0 ? 
                    recentLogs.map(log => `
                        <tr>
                            <td>${new Date(log.timestamp).toLocaleString()}</td>
                            <td><span class="badge badge-pending">${log.action}</span></td>
                            <td>${log.details}</td>
                        </tr>
                    `).join('') : '<tr><td colspan="3" style="text-align:center; color: var(--text-secondary);">No recent activity</td></tr>';

                document.getElementById('sidebar-active').textContent = stats.activeInterns;
                document.getElementById('sidebar-tasks').textContent = stats.totalTasks;
            },

            renderInterns() {
                let filtered = AppState.interns;
                
                if (AppState.filters.status) {
                    filtered = filtered.filter(i => i.status === AppState.filters.status);
                }
                if (AppState.filters.skill) {
                    filtered = filtered.filter(i => i.skills.includes(AppState.filters.skill));
                }

                const tbody = document.getElementById('interns-table');
                tbody.innerHTML = filtered.length > 0 ? filtered.map(intern => {
                    const taskCount = AppState.tasks.filter(t => t.assignedTo === intern.id).length;
                    return `
                        <tr>
                            <td><strong>${intern.id}</strong></td>
                            <td>${intern.name}</td>
                            <td>${intern.email}</td>
                            <td>
                                <div class="tags-container">
                                    ${intern.skills.map(s => `<span class="tag">${s}</span>`).join('')}
                                </div>
                            </td>
                            <td><span class="badge badge-${intern.status.toLowerCase()}">${intern.status}</span></td>
                            <td>${taskCount} tasks</td>
                            <td>
                                <div class="btn-group">
                                    ${intern.status === 'ONBOARDING' ? 
                                        `<button class="btn btn-small btn-success" onclick="App.activateIntern('${intern.id}')">Activate</button>` : ''}
                                    ${intern.status === 'ACTIVE' ? 
                                        `<button class="btn btn-small btn-danger" onclick="App.exitIntern('${intern.id}')">Exit</button>` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('') : '<tr><td colspan="7" style="text-align:center; color: var(--text-secondary);">No interns found</td></tr>';
            },

            renderTasks() {
                const tbody = document.getElementById('tasks-table');
                tbody.innerHTML = AppState.tasks.length > 0 ? AppState.tasks.map(task => {
                    const assignedIntern = AppState.interns.find(i => i.id === task.assignedTo);
                    const deps = task.dependencies?.map(id => {
                        const t = AppState.tasks.find(tk => tk.id === id);
                        return t ? t.title : id;
                    }).join(', ') || 'None';
                    
                    return `
                        <tr>
                            <td><strong>${task.id}</strong></td>
                            <td>${task.title}</td>
                            <td>
                                <div class="tags-container">
                                    ${task.requiredSkills.map(s => `<span class="tag">${s}</span>`).join('')}
                                </div>
                            </td>
                            <td>${assignedIntern ? assignedIntern.name : '<span class="text-muted">Unassigned</span>'}</td>
                            <td><span class="badge badge-${task.status.toLowerCase().replace('_', '-')}">${task.status}</span></td>
                            <td>${task.estimatedHours}h</td>
                            <td><small class="text-muted">${deps}</small></td>
                            <td>
                                <div class="btn-group">
                                    ${task.status === 'PENDING' && task.assignedTo ? 
                                        `<button class="btn btn-small btn-primary" onclick="App.startTask('${task.id}')">Start</button>` : ''}
                                    ${task.status === 'IN_PROGRESS' ? 
                                        `<button class="btn btn-small btn-success" onclick="App.completeTask('${task.id}')">Complete</button>` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('') : '<tr><td colspan="8" style="text-align:center; color: var(--text-secondary);">No tasks found</td></tr>';

                this.updateTaskSelects();
            },

            updateTaskSelects() {
                const activeInterns = AppState.interns.filter(i => i.status === 'ACTIVE');
                const assignSelect = document.getElementById('assign-select');
                assignSelect.innerHTML = '<option value="">Unassigned</option>' +
                    activeInterns.map(i => `<option value="${i.id}">${i.name} - [${i.skills.join(', ')}]</option>`).join('');

                const depsSelect = document.getElementById('dependencies-select');
                depsSelect.innerHTML = AppState.tasks.map(t => 
                    `<option value="${t.id}">${t.title} (${t.status})</option>`
                ).join('');
            },

            renderLogs() {
                const tbody = document.getElementById('logs-table');
                const logs = AppState.logs.slice().reverse();
                tbody.innerHTML = logs.length > 0 ? logs.map(log => `
                    <tr>
                        <td>${new Date(log.timestamp).toLocaleString()}</td>
                        <td><span class="badge badge-pending">${log.action}</span></td>
                        <td>${log.details}</td>
                    </tr>
                `).join('') : '<tr><td colspan="3" style="text-align:center; color: var(--text-secondary);">No logs available</td></tr>';
            }
        };
