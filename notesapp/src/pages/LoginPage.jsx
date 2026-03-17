import React from 'react';
import { GoogleLogin } from '@react-oauth/google';
import { jwtDecode } from 'jwt-decode';
import { BookText } from 'lucide-react';

const LoginPage = () => {
    const handleLoginSuccess = (credentialResponse) => {
        // 1. Unpack the secure token Google just sent us
        const decodedToken = jwtDecode(credentialResponse.credential);

        // 2. Extract the user's secure data
        const userData = {
            name: decodedToken.name,
            email: decodedToken.email,
            picture: decodedToken.picture
        };

        // 3. Save the user data to localStorage so they stay logged in!
        localStorage.setItem('currentUser', JSON.stringify(userData));

        // 4. Force reload to update the notes hook with the new user email
        window.location.href = '/';
    };

    return (
        <div style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: '100vh',
            backgroundColor: 'var(--bg)'
        }}>
            <div style={{
                background: 'white',
                padding: '40px',
                borderRadius: '12px',
                border: '1px solid var(--border)',
                textAlign: 'center',
                maxWidth: '400px',
                width: '100%'
            }}>
                <div style={{ display: 'flex', justifyContent: 'center', marginBottom: '20px', color: 'var(--primary)' }}>
                    <BookText size={48} />
                </div>
                <h1 style={{ marginBottom: '10px', fontSize: '1.5rem', fontWeight: 'bold' }}>Welcome to Notes</h1>
                <p style={{ color: 'var(--gray)', marginBottom: '30px' }}>Sign in to access your secure, private workspace.</p>

                {/* The magically pre-styled official Google Button */}
                <div style={{ display: 'flex', justifyContent: 'center' }}>
                    <GoogleLogin
                        onSuccess={handleLoginSuccess}
                        onError={() => {
                            console.log('Login Failed');
                            alert("Login failed, please try again.");
                        }}
                        theme="filled_blue"
                        shape="pill"
                    />
                </div>
            </div>
        </div>
    );
};

export default LoginPage;
