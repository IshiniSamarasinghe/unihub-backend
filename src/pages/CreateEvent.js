// src/pages/CreateEvent.js
import React, { useState } from 'react';
import './CreateEvent.css';

function CreateEvent() {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
      console.log('Selected file:', file.name);
      // Add preview/upload logic here if needed
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setIsSubmitting(true);

    // Simulate a delay for submission (you can replace this with actual logic)
    setTimeout(() => {
      console.log('Event created successfully');
      // Optionally reset form and state
      // setIsSubmitting(false);
    }, 3000);
  };

  return (
    <div className="create-event-container">
      {/* Left Side with Image and Welcome Message */}
      <div className="create-left">
        <img src="/assets/creation-img.png" alt="Illustration" className="event-image" />
      </div>

      {/* Right Side with Form */}
      <div className="create-right">
        <h1 className="logo">UniHub</h1>
        <p className="subtitle">Welcome to UniHub! Create events and share with your peers!</p>

        <form className="event-form" onSubmit={handleSubmit}>
          <div className="form-row">
            <input type="text" placeholder="Event Name :" />
            <input type="text" placeholder="Description:" />
          </div>

          <div className="form-row">
            <select>
              <option value="">Select your University:</option>
              <option value="kelaniya">University of Kelaniya</option>
              <option value="colombo">University of Colombo</option>
              <option value="peradeniya">University of Peradeniya</option>
              <option value="japura">University of Jayawardhanapura</option>
              <option value="moratuwa">University of Moratuwa</option>
              <option value="rajarata">University of Rajarata</option>
              <option value="eastern">Eastern University</option>
            </select>

            <select>
              <option value="">Select your Faculty:</option>
              <option value="fct">Faculty of Computing and Technology</option>
              <option value="humanities">Faculty of Humanities</option>
              <option value="social">Faculty of Social Science</option>
              <option value="physical">Faculty of Physical Science</option>
              <option value="management">Faculty of Management</option>
            </select>
          </div>

          <div className="form-row">
            <input type="date" />
            <input type="time" id="time" className="floating-input" />
          </div>

          <div className="form-row">
            <select>
              <option value="">Event Type:</option>
              <option value="physical">Physical</option>
              <option value="online">Online</option>
            </select>
            <input type="text" placeholder="Location:" />
          </div>

          <div className="form-row media-upload">
            <textarea placeholder="Add media:" readOnly />
            <input
              type="file"
              id="mediaUpload"
              accept=".jpg, .jpeg, .png"
              style={{ display: 'none' }}
              onChange={(e) => handleFileUpload(e)}
            />
            <div
              className="media-plus"
              onClick={() => document.getElementById('mediaUpload').click()}
            >
              +
            </div>
          </div>

          <div className="form-row">
            <select>
              <option value="">Audience Type:</option>
              <option value="all">Open to all</option>
              <option value="uni">Only for University students</option>
              <option value="faculty">Only for faculty students</option>
            </select>
            <input type="text" placeholder="Hosting Society:" />
            <select>
              <option value="">Event Approver:</option>
              <option value="president">President</option>
              <option value="vp">Vice President</option>
              <option value="treasurer">Senior Treasurer</option>
            </select>
          </div>

          <div className="form-row button-row">
            <button type="submit" className="create-btn" disabled={isSubmitting}>
              {isSubmitting ? (
                <>
                  <span className="spinner"></span> Pending...
                </>
              ) : (
                "Create Event"
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default CreateEvent;
