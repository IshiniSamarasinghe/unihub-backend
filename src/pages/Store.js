import React, { useState } from 'react';
import './Store.css';

const storeItems = [
  {
    id: 1,
    title: 'Shield Tshirt',
    description: 'We are taking pre orders now! This is your chance.',
    image: '/assets/store/tshirt.jpeg',
    faculty: 'Faculty of Computing and Technology',
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

function Store() {
  const [selectedItem, setSelectedItem] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [formValues, setFormValues] = useState({
    title: '',
    faculty: '',
    description: '',
    price: '',
    details: '',
    image: null
  });

  const handleInputChange = (e) => {
    const { name, value, files } = e.target;
    if (name === 'image') {
      const file = files[0];
      if (file && !['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
        alert('Only JPG, JPEG, or PNG files are allowed.');
        return;
      }
      setFormValues({ ...formValues, image: file });
    } else {
      setFormValues({ ...formValues, [name]: value });
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log('Item submitted:', formValues);
    setShowForm(false);
    setFormValues({
      title: '',
      faculty: '',
      description: '',
      price: '',
      details: '',
      image: null
    });
    alert('Item submitted! (Currently only logs to console)');
  };

  return (
    <div className="store-wrapper">
      <div className={`store-container ${selectedItem || showForm ? 'blurred' : ''}`}>
        <h2>Support Student Fundraisers</h2>

        <div className="add-item-btn-wrapper">
          <button className="add-item-btn" onClick={() => setShowForm(true)}>
            Add Your Item +
          </button>
        </div>

        <div className="store-grid">
          {storeItems.map(item => (
            <div className="store-card" key={item.id}>
              <img src={item.image} alt={item.title} className="store-image" />
              <h3>{item.title}</h3>
              <p className="item-description">{item.description}</p>
              <button className="buy-btn" onClick={() => setSelectedItem(item)}>View</button>
            </div>
          ))}
        </div>
      </div>

      {/* View Details Modal */}
      {selectedItem && (
        <div className="store-modal">
          <div className="modal-content">
            <span className="close-modal" onClick={() => setSelectedItem(null)}>&times;</span>
            <img src={selectedItem.image} alt={selectedItem.title} />
            <h3>{selectedItem.title}</h3>
            <h5>{selectedItem.faculty}</h5>
            <p>{selectedItem.description}</p>
            <p>{selectedItem.price}</p>
            <p>{selectedItem.details}</p>
          </div>
        </div>
      )}

      {/* Add Item Form Modal */}
      {showForm && (
        <div className="store-modal1">
          <div className="modal-content1">
            <span className="close-modal" onClick={() => setShowForm(false)}>&times;</span>
            <h3>Add Your Fundraising Item</h3>
            <form className="add-item-form" onSubmit={handleSubmit}>
              <input type="text" name="title" placeholder="Title" value={formValues.title} onChange={handleInputChange} required />
              <input type="text" name="faculty" placeholder="Faculty" value={formValues.faculty} onChange={handleInputChange} required />
              <input type="text" name="description" placeholder="Description" value={formValues.description} onChange={handleInputChange} required />
              <input type="text" name="price" placeholder="Price" value={formValues.price} onChange={handleInputChange} required />
              <input type="text" name="details" placeholder="WhatsApp or Contact Details" value={formValues.details} onChange={handleInputChange} required />
              <input type="file" name="image" accept=".jpg,.jpeg,.png" onChange={handleInputChange} required />
              <button type="submit">Submit Item</button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

export default Store;
