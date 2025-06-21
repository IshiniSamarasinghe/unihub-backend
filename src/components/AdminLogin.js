import React, { useState } from 'react';
import './AdminLogin.css';
import { useNavigate } from 'react-router-dom';
import { Link } from 'react-router-dom';


function AdminLogin() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const navigate = useNavigate();

    const handleLogin = (e) => {
        e.preventDefault();

        // Simple mock login logic (replace with backend call later)
        if (email === 'admin@unihub.lk' && password === 'admin123') {
            navigate('/admin/dashboard');
        } else {
            alert('Invalid credentials!');
        }
    };

    return (
        <div className="admin-login-container">
            <div className="login-box">
                <h1>UniHub</h1>
                <h2>Admin Login</h2>
                <form onSubmit={handleLogin}>
                    <input
                        type="email"
                        placeholder="Email address"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                    <input
                        type="password"
                        placeholder="Password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                    <p className="forgot-password-link">
                        <Link to="/admin/change-password">Forgot Password?</Link>
                    </p>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    );
}

export default AdminLogin;
