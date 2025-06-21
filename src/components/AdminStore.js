import React from 'react';
import './AdminStore.css';
import AdminLayout from './AdminLayout';
import { FaEdit, FaTrash } from 'react-icons/fa';

function AdminStore() {
  const storeItems = [
    {
      id: 1,
      title: 'Shield T-shirt',
      description: 'We are taking pre orders now! This is your chance.',
      image: '/assets/store/tshirt.jpeg',
      faculty: 'Faculty of computing and technology',
      price: 'Rs. 1200',
      details: 'WhatsApp Us - 076 3514818',
    },
    {
      id: 2,
      title: 'Stillness of Night',
      description: 'Custom-made bookmark to raise event funds.',
      image: '/assets/store/bookmark.png',
      faculty: 'Faculty of Computing and Technology',
      price: 'Rs. 50',
      details: 'WhatsApp Us - 077 6237016',
    },
    {
      id: 3,
      title: 'ITSA Fundraising Stickers',
      description: 'Sticker pack for your laptop or notebook!',
      image: '/assets/store/stickers.jpg',
      faculty: 'Faculty of Computing and Technology',
      price: 'Starting from Rs. 10/=',
      details: 'WhatsApp Us - 076 3514818',
    },
  ];

  return (
    <AdminLayout activePage="store">
      <div className="admin-store-page">
        <div className="store-header">
          <h2>Store Management</h2>
        </div>

        <table className="store-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Description</th>
              <th>Image</th>
              <th>Faculty</th>
              <th>Price</th>
              <th>Details</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {storeItems.map((item) => (
              <tr key={item.id}>
                <td>{item.id}</td>
                <td>{item.title}</td>
                <td>{item.description}</td>
                <td>
                  <img
                    src={item.image}
                    alt={item.title}
                    style={{ width: '60px', borderRadius: '4px' }}
                  />
                </td>
                <td>{item.faculty}</td>
                <td>{item.price}</td>
                <td>{item.details}</td>
                <td>
                  <button className="edit-btn1"><FaEdit /></button>
                  <button className="delete-btn1"><FaTrash /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AdminLayout>
  );
}

export default AdminStore;
