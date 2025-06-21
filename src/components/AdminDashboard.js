// src/components/AdminDashboard.js
import React from 'react';
import './AdminDashboard.css';
import { FaClipboardList, FaClock, FaUsers } from 'react-icons/fa';
import AdminLayout from './AdminLayout';
import './AdminDashboard.css';

function AdminDashboard() {
  return (
    <AdminLayout activePage="dashboard">
      <div className="admin-topbar">
        <div className="search-bar">
          <input type="text" placeholder="Quick search" />
        </div>
      </div>

      {/* Stat cards */}
      <div className="dashboard-cards">
        <div className="dashboard-card active">
          <FaClipboardList className="card-icon" />
          <h4>Total Events</h4>
          <p>128</p>
        </div>
        <div className="dashboard-card">
          <FaClock className="card-icon" />
          <h4>Pending Approvals</h4>
          <p>12</p>
        </div>
        <div className="dashboard-card">
          <FaUsers className="card-icon" />
          <h4>Registered Users</h4>
          <p>65</p>
        </div>
      </div>

      {/* Recent submissions */}
      <div className="recent-submissions">
        <h3>Recent Event Submissions</h3>
        <table>
          <thead>
            <tr>
              <th>Event</th>
              <th>University</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            {[...Array(6)].map((_, index) => (
              <tr key={index}>
                <td>CodeFest 2025</td>
                <td>Kelaniya</td>
                <td>2025-07-15</td>
                <td><span className="status pending">Pending</span></td>
              </tr>
            ))}
          </tbody>
        </table>

        <div className="submission-actions">
          <button className="clear-btn">Clear</button>
          <button className="view-all-btn">View all</button>
        </div>
      </div>
    </AdminLayout>
  );
}

export default AdminDashboard;
