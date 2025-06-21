import React, { useState } from 'react';
import './SearchBar.css';
import { FiSearch } from 'react-icons/fi';

function SearchBar({ onSearch }) {
  const [query, setQuery] = useState('');

  const handleInputChange = (e) => {
    const value = e.target.value;
    setQuery(value);
    onSearch(value); // Trigger parent filter
  };

  return (
    <div className="search-container">
      <input
        type="text"
        placeholder="Search"
        className="search-input"
        value={query}
        onChange={handleInputChange}
      />
      <button className="search-button">
        <FiSearch size={16} />
      </button>
    </div>
  );
}

export default SearchBar;
