// src/components/AdminLayout.js
import React from 'react';
import { FaClipboardList, FaClock, FaUsers, FaSignOutAlt, FaLock } from 'react-icons/fa';
import { Link, useLocation } from 'react-router-dom';
import './AdminLayout.css';
import { FaStore } from 'react-icons/fa';


function AdminLayout({ children, activePage }) {

    const location = useLocation();

    return (
        <div className="admin-layout">
            {/* Sidebar */}
            <aside className="admin-sidebar">
                <div className="admin-profile">
                    <img src="/assets/admin/admin1.png" alt="admin" className="admin-avatar" />
                    <span>Welcome Ishini!</span>
                </div>
                <nav className="admin-menu">
                    <Link to="/admin/dashboard" className={`menu-item ${location.pathname === "/admin/dashboard" ? "active" : ""}`}>
                        <FaClipboardList /> Dashboard
                    </Link>
                    <Link to="/admin/events" className={`menu-item ${location.pathname === "/admin/events" ? "active" : ""}`}>
                        <FaClipboardList /> All Events
                    </Link>
                    <Link to="/admin/approvals" className={`menu-item ${location.pathname === "/admin/approvals" ? "active" : ""}`}>
                        <FaClock /> Pending Approvals
                    </Link>
                    <Link to="/admin/users" className={`menu-item ${location.pathname === "/admin/users" ? "active" : ""}`}>
                        <FaUsers /> Users
                    </Link>
                    <Link to="/admin/societies" className={`menu-item ${location.pathname === "/admin/societies" ? "active" : ""}`}>
                        <FaClipboardList /> Societies
                    </Link>
                    <Link to="/admin/store" className={`menu-item ${activePage === 'store' ? 'active' : ''}`}>
                        <FaStore /> Store
                    </Link>
                    <Link to="/admin/change-password" className={`menu-item ${location.pathname === "/admin/change-password" ? "active" : ""}`}>
                        <FaLock /> Change Password
                    </Link>
                    <Link to="/admin/logout" className="menu-item">
                        <FaSignOutAlt /> Logout
                    </Link>
                </nav>
                <div className="admin-footer">
                    <h4>UniHub</h4>
                    <p>Version: 1.0.0.0</p>
                </div>
            </aside>

            {/* Right Side Content */}
            <main className="admin-main">
                {children}
            </main>
        </div>
    );
}

export default AdminLayout;
