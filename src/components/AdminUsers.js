import React from 'react';
import './AdminUsers.css';
import AdminLayout from './AdminLayout';
import { FaUserEdit, FaTrash } from 'react-icons/fa';

function AdminUsers() {
  return (
    <AdminLayout activePage="users">
      <div className="user-management">
        <h3>Registered Users</h3>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>University</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {[...Array(5)].map((_, index) => (
              <tr key={index}>
                <td>Student {index + 1}</td>
                <td>Moratuwa</td>
                <td>student{index + 1}@unihub.lk</td>
                <td>{index % 2 === 0 ? 'Normal' : 'Super User'}</td>
                <td>
                  <button className="edit-btn"><FaUserEdit /></button>
                  <button className="delete-btn"><FaTrash /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        <div className="submission-actions">
          <button className="clear-btn">Clear</button>
          <button className="view-all-btn">Export</button>
        </div>
      </div>
    </AdminLayout>
  );
}

export default AdminUsers;
