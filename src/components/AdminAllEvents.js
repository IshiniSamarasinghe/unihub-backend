import React from 'react';
import './AdminAllEvents.css';
import AdminLayout from './AdminLayout';
import { Link } from 'react-router-dom';

function AdminAllEvents() {
  return (
    <AdminLayout active="events">
      <div className="all-events-section">
        <h2>All Events</h2>
        <table>
          <thead>
            <tr>
              <th>Event</th>
              <th>University</th>
              <th>Date</th>
              <th>Society</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {[...Array(5)].map((_, index) => (
              <tr key={index}>
                <td>CodeFest 2025</td>
                <td>Kelaniya</td>
                <td>2025-07-15</td>
                <td>ITSA</td>
                <td><span className="status approved">Approved</span></td>
                <td>
                  {/* ✅ View Button as a Link */}
                  
                  <button className="edit-btn">Edit</button>
                  <button className="delete-btn">Delete</button>
                   <Link to={`/event/codefest-2025`} className="view-btn">View</Link>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AdminLayout>
  );
}

export default AdminAllEvents;
