import React from 'react';
import './AdminSocieties.css';
import AdminLayout from './AdminLayout';
import { FaEdit, FaTrash } from 'react-icons/fa';

function AdminSocieties() {
  return (
    <AdminLayout activePage="societies">
      <div className="society-management">
        <h3>Registered Societies</h3>
        <table>
          <thead>
            <tr>
              <th>Society Name</th>
              <th>Faculty</th>
              <th>University</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {[...Array(5)].map((_, index) => (
              <tr key={index}>
                <td>ITSA</td>
                <td>Computing and Technology</td>
                <td>Kelaniya</td>
                <td>
                  <button className="edit-btn2"><FaEdit /></button>
                  <button className="delete-btn2"><FaTrash /></button>
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

export default AdminSocieties;
