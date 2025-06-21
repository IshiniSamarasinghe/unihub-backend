// src/pages/SignUp.js

import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from '../axios';
import './SignUp.css';

function SignUp() {
  const [roles, setRoles] = useState([{ society: '', role: '' }]);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    phone: '',
    faculty: '',
  });
  const [status, setStatus] = useState('idle'); // idle | loading | success | error
  const [showPassword, setShowPassword] = useState(false);
  const navigate = useNavigate();

  const handleAddRole = () => {
    setRoles([...roles, { society: '', role: '' }]);
  };

  const handleInputChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleRoleChange = (index, field, value) => {
    const updatedRoles = [...roles];
    updatedRoles[index][field] = value;
    setRoles(updatedRoles);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setStatus('loading');

    try {
      await axios.get('/sanctum/csrf-cookie');

      await axios.post('/api/register', {
        ...formData,
        roles,
      });

      setStatus('success');
      console.log('✅ Registration success');

      setFormData({
        name: '',
        email: '',
        password: '',
        phone: '',
        faculty: '',
      });
      setRoles([{ society: '', role: '' }]);

      setTimeout(() => setStatus('idle'), 3000);
    } catch (error) {
      const details = error.response?.data?.details;
      setStatus('error');

      if (error.response?.status === 422 && details) {
        const firstError = Object.values(details)[0][0];
        alert(`Validation failed: ${firstError}`);
      } else {
        alert('❌ Registration failed: ' + (error.response?.data?.error || error.message));
      }

      setTimeout(() => setStatus('idle'), 3000);
    }
  };

  return (
    <div className="signup-container">
      <div className="signup-left">
        <h1 className="logo">UniHub</h1>
        <p className="welcome-text">
          Welcome to UniHub! Sign Up and enjoy with creating and sharing events.
        </p>
        <img
          src="/assets/signup-img.png"
          alt="Illustration"
          className="signup-illustration"
        />
      </div>

      <div className="signup-right">
        <h2>Create New Account</h2>
        <form className="signup-form" onSubmit={handleSubmit}>
          <input
            name="name"
            value={formData.name}
            onChange={handleInputChange}
            placeholder="Name"
            required
          />
          <input
            name="email"
            type="email"
            value={formData.email}
            onChange={handleInputChange}
            placeholder="University Email"
            required
          />

          {/* Password with Show/Hide Toggle */}
          <div className="password-field" style={{ position: 'relative' }}>
            <input
              name="password"
              type={showPassword ? 'text' : 'password'}
              value={formData.password}
              onChange={handleInputChange}
              placeholder="Password"
              required
              style={{ paddingRight: '2rem' }}
            />
            <span
              onClick={() => setShowPassword((prev) => !prev)}
              style={{
                position: 'absolute',
                right: '10px',
                top: '50%',
                transform: 'translateY(-50%)',
                cursor: 'pointer',
              }}
              title={showPassword ? 'Hide password' : 'Show password'}
            >
              {showPassword ? '🙈' : '👁️'}
            </span>
          </div>

          <input
            name="phone"
            value={formData.phone}
            onChange={handleInputChange}
            placeholder="Phone Number"
            required
          />
          <input
            name="faculty"
            value={formData.faculty}
            onChange={handleInputChange}
            placeholder="Faculty"
            required
          />

          {roles.map((role, index) => (
            <div className="role-pair" key={index}>
              <select
                value={role.society}
                onChange={(e) => handleRoleChange(index, 'society', e.target.value)}
                required
              >
                <option value="">Select Society</option>
                <option value="itsa">ITSA</option>
                <option value="cssa">CSSA</option>
                <option value="dancing">Dancing Club</option>
                <option value="isaca">ISACA</option>
                <option value="foss">FOSS</option>
              </select>
              <select
                value={role.role}
                onChange={(e) => handleRoleChange(index, 'role', e.target.value)}
                required
              >
                <option value="">Select Role</option>
                <option value="president">President</option>
                <option value="vp">Vice President</option>
                <option value="jt">Junior Treasurer</option>
                <option value="sec">Secretary</option>
                <option value="editor">Co-editor</option>
                <option value="committee">Committee Member</option>
                <option value="member">Member</option>
              </select>
            </div>
          ))}

          <button type="button" className="add-role" onClick={handleAddRole}>
            Add another Role +
          </button>

          <div className="terms">
            <input type="checkbox" className="box" required />
            <label className="terms-text">
              I agree to the terms & conditions.
            </label>
          </div>

          <button type="submit" className="signup-btn" disabled={status === 'loading'}>
            {status === 'loading'
              ? 'Signing Up...'
              : status === 'success'
              ? '✅ Registered!'
              : 'Sign Up'}
          </button>

          {/* ✅ Login link */}
          <p style={{ marginTop: '1rem', fontSize: '10px', textAlign: 'center' }}>
            Already have an account?{' '}
            <span
              onClick={() => navigate('/signin')}
              style={{ color: '#007bff', cursor: 'pointer', textDecoration: 'underline', fontFamily: 'poppins' }}
            >
              Login here
            </span>
          </p>
        </form>
      </div>
    </div>
  );
}

export default SignUp;
