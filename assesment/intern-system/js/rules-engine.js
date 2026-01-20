const RulesEngine = {
            canTransitionStatus(current, next) {
                const transitions = {
                    'ONBOARDING': ['ACTIVE'],
                    'ACTIVE': ['EXITED'],
                    'EXITED': []
                };
                return transitions[current]?.includes(next) || false;
            },

            canAssignTask(intern, task) {
                if (intern.status !== 'ACTIVE') return false;
                return task.requiredSkills.every(skill => intern.skills.includes(skill));
            },

            detectCircularDependency(taskId, dependencies, allTasks) {
                const visited = new Set();
                const stack = new Set();

                const hasCycle = (id) => {
                    if (stack.has(id)) return true;
                    if (visited.has(id)) return false;

                    visited.add(id);
                    stack.add(id);

                    const task = allTasks.find(t => t.id === id);
                    if (task?.dependencies) {
                        for (let depId of task.dependencies) {
                            if (hasCycle(depId)) return true;
                        }
                    }

                    stack.delete(id);
                    return false;
                };

                return hasCycle(taskId);
            },

            canCompleteTask(task, allTasks) {
                if (!task.dependencies?.length) return true;
                return task.dependencies.every(depId => {
                    const dep = allTasks.find(t => t.id === depId);
                    return dep?.status === 'DONE';
                });
            }
        };