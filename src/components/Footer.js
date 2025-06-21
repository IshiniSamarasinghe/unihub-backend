import React from 'react';
import './Footer.css';

function Footer() {
  return (
    <footer className="footer">
      <div className="footer-top">
        {/* UniHub Branding */}
        <div className="footer-column">
          <h3 className="footer-title">UniHub</h3>
          <p className="footer-description">
            UniHub is your all-in-one platform to discover, share, and manage university events.
            Stay connected, get involved, and make the most of your campus life.
          </p>
        </div>

        {/* Menu Links */}
        <div className="footer-column">
          <h4 className="footer-subtitle2">Menu</h4>
          <ul className="footer-links">
            <li><a href="/">Home</a></li>
            <li><a href="/events">Events</a></li>
            <li><a href="/about">About</a></li>
          </ul>
        </div>

        {/* Subscribe */}
        <div className="footer-column">
          <h4 className="footer-subtitle">Subscribe</h4>
          <form className="subscribe-form" onSubmit={(e) => e.preventDefault()}>
            <input type="email" placeholder="Email..." required />
            <button type="submit">➤</button>
          </form>
        </div>
      </div>

      <div className="footer-bottom">
        <p>© UniHub All Rights Reserved 2025</p>
      </div>
    </footer>
  );
}

export default Footer;
