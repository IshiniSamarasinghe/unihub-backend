import React from 'react';
import './About.css';
import UniversityLogos from '../components/UniversityLogos';
import EventPromo from '../components/EventPromo';
import { Link } from 'react-router-dom';


function About() {
    return (
        <div>
            {/* Hero Section */}
            <div
                className="about-hero"
                style={{
                    backgroundImage: "url('/assets/hero-image2.png')",
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    height: '140px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: '#000',
                }}
            >
                <h1 className="about-title">ABOUT US</h1>
            </div>

            {/* Why Us Section */}
            <section className="about-section">
                <div className="about-text">
                    <h5>WHY US</h5>
                    <h2>Making Campus Life Connected</h2>
                    <p>
                        University students often miss out on events due to scattered communication.
                        UniHub brings everything together in one place — built for better awareness,
                        engagement, and student collaboration.
                    </p>
                    <Link to="/events" className="orange-button">View Events</Link>

                </div>
                <div className="about-image">
                    <img src="/assets/aboutus/aboutus-img1.png" alt="Chatting Illustration" />
                </div>
            </section>

            {/* University Logos */}
            <UniversityLogos />

            {/* Our Goal Section */}
            <section className="about-section reverse">
                <div className="about-image">
                    <img src="/assets/aboutus/aboutus-img2.png" alt="Music Illustration" />
                </div>
                <div className="about-text">
                    <h5>OUR GOAL</h5>
                    <h2>Connecting Students Through Events</h2>
                    <p>
                        To create a centralized, student-friendly platform for discovering, managing,
                        and sharing university events — with real-time updates and easy access for all.
                    </p>
                    <Link to="/create-event"> 
                    <button className="orange-button">Create Event</button>
                    </Link>
                </div>
            </section>

            {/* Create Posts Section */}
            <EventPromo />
        </div>
    );
}

export default About;
