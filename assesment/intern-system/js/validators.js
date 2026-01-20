 const Validators = {
            validateIntern(data) {
                const errors = [];
                if (!data.name?.trim() || data.name.trim().length < 2) {
                    errors.push('Name must be at least 2 characters');
                }
                if (!data.email || !Validators.isValidEmail(data.email)) {
                    errors.push('Valid email is required');
                }
                if (!data.skills?.length) {
                    errors.push('At least one skill is required');
                }
                return errors;
            },

            validateTask(data) {
                const errors = [];
                if (!data.title?.trim() || data.title.trim().length < 3) {
                    errors.push('Title must be at least 3 characters');
                }
                if (!data.estimatedHours || data.estimatedHours < 1) {
                    errors.push('Estimated hours must be at least 1');
                }
                if (!data.requiredSkills?.length) {
                    errors.push('At least one required skill needed');
                }
                return errors;
            },

            isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
        };