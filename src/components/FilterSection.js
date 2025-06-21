import React, { useState } from 'react';
import './FilterSection.css';
import { FaFilter, FaTimes } from 'react-icons/fa';

function FilterSection({ onFilter, onClear }) {
  const [university, setUniversity] = useState('');
  const [faculty, setFaculty] = useState('');
  const [society, setSociety] = useState('');

  const handleFilterClick = () => {
    onFilter({ university, faculty, society });
  };

  const handleClear = () => {
    setUniversity('');
    setFaculty('');
    setSociety('');
    onClear();
  };

  return (
    <div className="filter-container">
      <select className="dropdown" value={university} onChange={(e) => setUniversity(e.target.value)}>
        <option value="">By University</option>
        <option value="Kelaniya">Kelaniya</option>
        <option value="Colombo">Colombo</option>
        <option value="Moratuwa">Moratuwa</option>
        <option value="Peradeniya">Peradeniya</option>
        <option value="Jaffna">Jaffna</option>
        <option value="Ruhuna">Ruhuna</option>
        <option value="Jayawardhanapura">Jayawardhanapura</option>
      </select>

      <select className="dropdown" value={faculty} onChange={(e) => setFaculty(e.target.value)}>
        <option value="">By Faculty</option>
        <option value="Faculty of Computing and Technology">Faculty of Computing and Technology</option>
        <option value="Faculty of Humanities">Faculty of Humanities</option>
        <option value="Faculty of Social Science">Faculty of Social Science</option>
        <option value="Faculty of Management">Faculty of Management</option>
        <option value="Faculty of Physical Science">Faculty of Physical Science</option>
      </select>

      <select className="dropdown" value={society} onChange={(e) => setSociety(e.target.value)}>
        <option value="">By Society</option>
        <option value="ITSA">ITSA</option>
        <option value="ETSA">ETSA</option>
        <option value="CSSA">CSSA</option>
        <option value="Dancing club">Dancing club</option>
        <option value="ISACA">ISACA</option>
      </select>

      <button className="filter-button" onClick={handleFilterClick}>
        <FaFilter style={{ marginRight: '6px' }} />
        Filter
      </button>

      <button className="filter-button clear" onClick={handleClear}>
        <FaTimes style={{ marginRight: '6px' }} />
        Clear
      </button>
    </div>
  );
}

export default FilterSection;
