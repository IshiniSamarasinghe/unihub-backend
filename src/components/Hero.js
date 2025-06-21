import React from 'react';

function Hero() {
  const handleScroll = () => {
    const section = document.getElementById('event-section');
    if (section) {
      section.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <section
      style={{
        backgroundImage: "url('/assets/hero-image2.png')",
        backgroundSize: 'cover',
        backgroundColor: 'rgba(0, 0, 0, 0.1)',
        backgroundPosition: 'center',
        height: '60vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        flexDirection: 'column',
        color: 'black',
        textAlign: 'center',
        padding: '0 1rem',
      }}
    >
      <h1
        style={{
          fontSize: '2rem',
          marginBottom: '0.4rem',
          marginTop: '0.1rem',
          fontFamily: 'Playfair Display, serif',
          fontWeight: 'normal',
        }}
      >
        Welcome to UniHub<br />Your Campus, Connected.
      </h1>

      <p
        style={{
          fontSize: '0.7rem',
          maxWidth: '700px',
          marginBottom: '1.5rem',
          marginTop: '0.1rem',
        }}
      >
        Discover, explore, and engage with the latest events at your university. Whether you're looking to attend workshops, join club meetups, or showcase your own events, UniHub brings everything to one place — built just for students like you.
      </p>

      <button
        style={{
          backgroundColor: '#fff',
          color: '#000',
          padding: '0.5rem 1rem',
          borderRadius: '5px',
          fontWeight: 'regular',
          border: 'none',
          cursor: 'pointer',
          transition: 'all 0.3s ease',
          fontSize: '12px',
          fontFamily: 'Poppins, sans-serif',
        }}
        onClick={handleScroll}
        onMouseEnter={(e) => {
          e.target.style.backgroundColor = '#EAD8B1';
          e.target.style.color = '#000';
        }}
        onMouseLeave={(e) => {
          e.target.style.backgroundColor = '#fff';
          e.target.style.color = '#000';
        }}
      >
        Explore Events
      </button>
    </section>
  );
}

export default Hero;
