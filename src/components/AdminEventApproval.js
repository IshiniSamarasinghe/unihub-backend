import React from 'react';
import './AdminEventApproval.css';
import AdminLayout from './AdminLayout';

function AdminEventApproval() {
  return (
    <AdminLayout active="approvals">
      <div className="approval-section">
        <h2>Pending Event Approvals</h2>
        <table>
          <thead>
            <tr>
              <th>Event</th>
              <th>Society</th>
              <th>Date</th>
              <th>Approver</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            {[...Array(5)].map((_, index) => (
              <tr key={index}>
                <td>CodeFest 2025</td>
                <td>ITSA</td>
                <td>2025-07-20</td>
                <td>President</td>
                <td><span className="status pending">Pending</span></td>
                <td>
                  <button className="approve-btn">Approve</button>
                  <button className="reject-btn">Reject</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AdminLayout>
  );
}

export default AdminEventApproval;
