import React, { useState } from 'react';
import Hero from '../components/Hero';
import UniversityLogos from '../components/UniversityLogos';
import FilterSection from '../components/FilterSection';
import SearchBar from '../components/SearchBar';
import EventGrid from '../components/EventGrid';
import EventPromo from '../components/EventPromo';

function Home() {
  // All hardcoded events
  const allEvents = [
    {
      image: '/assets/events/1.jpeg',
      title: 'Sneha Warsha 2025',
      university: 'Kelaniya',
      faculty: 'Faculty of Computing and Technology',
      society: ' ',
    },
    {
      image: '/assets/events/2.jpeg',
      title: 'Sankramana 2025',
      university: 'Kelaniya',
      faculty: ' ',
      society: 'Union of Students association ',
    },
    {
      image: '/assets/events/3.jpeg',
      title: 'Champions League 2024',
      university: 'Kelaniya',
      faculty: 'Faculty of Computing and Technology',
      society: 'Union of Students association',
    },
    {
      image: '/assets/events/4.jpeg',
      title: 'Thun Dola 2024',
      university: 'Kelaniya',
      faculty: 'Faculty of Commerce and Management Studies',
      society: 'Faculty of Commerce and Management Studies and union student council',
    },
  ];

  // State variables
  const [filteredEvents, setFilteredEvents] = useState(allEvents);
  const [filters, setFilters] = useState({ university: '', faculty: '', society: '' });
  const [searchQuery, setSearchQuery] = useState('');

  // Main function to apply both filters and search
  const applyAllFilters = (activeFilters, search) => {
    const filtered = allEvents.filter((event) =>
      (!activeFilters.university || event.university === activeFilters.university) &&
      (!activeFilters.faculty || event.faculty === activeFilters.faculty) &&
      (!activeFilters.society || event.society === activeFilters.society) &&
      (!search || event.title.toLowerCase().includes(search.toLowerCase()))
    );
    setFilteredEvents(filtered);
  };

  // Filter trigger
  const handleFilter = (filterValues) => {
    setFilters(filterValues);
    applyAllFilters(filterValues, searchQuery);
  };

  // Search trigger
  const handleSearch = (query) => {
    setSearchQuery(query);
    applyAllFilters(filters, query);
  };

  // Reset all
  const handleClear = () => {
    setFilters({ university: '', faculty: '', society: '' });
    setSearchQuery('');
    setFilteredEvents(allEvents);
  };

  return (
    <>
      <Hero />
      <UniversityLogos />
      <FilterSection onFilter={handleFilter} onClear={handleClear} />
      <SearchBar onSearch={handleSearch} />
      <EventGrid events={filteredEvents} />
      <EventPromo />
    </>
  );
}

export default Home;
