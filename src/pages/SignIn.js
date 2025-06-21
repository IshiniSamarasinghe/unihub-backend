// src/pages/SignIn.js

import React from 'react';
import './SignIn.css';
 

function SignIn() {
    return (
        <div className="signin-container">
            {/* Left Section */}
            <div className="signin-left">
                <h1 className="logo">UniHub</h1>
                <p className="welcome-text">Nice to see you again!</p>
                <img src="/assets/signin-img.png" alt="Illustration" className="signin-illustration" />
            </div>

            {/* Right Section */}
            <div className="signin-right">
                <h2>Login</h2>
                <form className="signin-form">
                    <input type="text" placeholder="Email or phone number" />
                    <input type="password" placeholder="Enter Your Password" />
                    <div className="signin-options">
                        <label className="remember-me">
                            <input type="checkbox" /> Remember me
                        </label>
                        <a href="/" className="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" className="signin-btn">Sign in</button>
                    <p className="signup-redirect">
                        Do you have an account? <a href="/signup">Create an Account</a>
                    </p>
                </form>
            </div>
        </div>
    );
}

export default SignIn;
