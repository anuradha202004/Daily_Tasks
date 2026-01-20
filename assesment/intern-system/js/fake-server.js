 const FakeServer = {
            checkEmailUnique: async (email) => {
                await FakeServer.simulateDelay(500);
                return !AppState.interns.some(i => i.email.toLowerCase() === email.toLowerCase());
            },
            simulateDelay: (ms) => new Promise(resolve => setTimeout(resolve, ms))
        };

       