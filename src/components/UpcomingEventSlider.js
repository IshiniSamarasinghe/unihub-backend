import React, { useRef, useEffect } from 'react';
import './EventGrid.css';
import EventCard from './EventCard';
import { FaChevronLeft, FaChevronRight } from 'react-icons/fa';

const upcomingEvents = [
  {
    image: '/assets/events/1.jpeg',
    title: 'Sneha Warsha 2025',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/2.jpeg',
    title: 'Sankramana 2025',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/3.jpeg',
    title: 'Champions League 2024',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/4.jpeg',
    title: 'Thun Dola 2024',
    university: 'University of Jaffna',
  },
    {
    image: '/assets/events/5.jpeg',
    title: 'IDEATHON 4.0',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/6.jpeg',
    title: 'Frostipia 2024',
    university: 'University of Kelaniya',
  },
 
];

function UpcomingEventSlider() {
  const sliderRef = useRef(null);

  const scrollByAmount = 300;

  const scrollLeft = () => {
    sliderRef.current.scrollBy({ left: -scrollByAmount, behavior: 'smooth' });
  };

  const scrollRight = () => {
    sliderRef.current.scrollBy({ left: scrollByAmount, behavior: 'smooth' });
  };

  useEffect(() => {
    const interval = setInterval(() => {
      if (!sliderRef.current) return;

      const { scrollLeft, scrollWidth, clientWidth } = sliderRef.current;

      if (scrollLeft + clientWidth >= scrollWidth - 10) {
        // Go back to start
        sliderRef.current.scrollTo({ left: 0, behavior: 'smooth' });
      } else {
        // Scroll forward
        sliderRef.current.scrollBy({ left: scrollByAmount, behavior: 'smooth' });
      }
    }, 3000); // scroll every 3 seconds

    return () => clearInterval(interval);
  }, []);

  return (
    <div className="slider-section">
      <h2 className="slider-heading">UPCOMING EVENTS</h2>
      <div className="slider-wrapper">
        <button className="slider-arrow left" onClick={scrollLeft}>
          <FaChevronLeft />
        </button>

        <div className="event-slider" ref={sliderRef}>
          {upcomingEvents.map((event, index) => (
            <EventCard
              key={index}
              image={event.image}
              title={event.title}
              university={event.university}
            />
          ))}
        </div>

        <button className="slider-arrow right" onClick={scrollRight}>
          <FaChevronRight />
        </button>
      </div>
    </div>
  );
}

export default UpcomingEventSlider;
