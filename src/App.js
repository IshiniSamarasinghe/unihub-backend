import { BrowserRouter as Router, Routes, Route, useLocation } from 'react-router-dom';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import Home from './pages/Home';
import UpcomingEvents from './pages/UpcomingEvents';
import PastEvents from './pages/PastEvents';
import About from './pages/About';
import SignUp from './pages/SignUp';
import EventDetails from './pages/EventDetails';
import SignIn from './pages/SignIn';
import CreateEvent from './pages/CreateEvent';
import Store from './pages/Store';



// Admin components
import AdminDashboard from './components/AdminDashboard';
import AdminEventApproval from './components/AdminEventApproval';
import AdminAllEvents from './components/AdminAllEvents'; // ✅ Import added
import AdminUsers from './components/AdminUsers';
import AdminSocieties from './components/AdminSocieties';
import AdminLogin from './components/AdminLogin';
import AdminChangePassword from './components/AdminChangePassword';
import AdminStore from './components/AdminStore';



function LayoutWrapper({ children }) {
  const location = useLocation();

  // ✅ Hide layout for public-only or admin pages
  const hideLayout = location.pathname.startsWith('/admin') ||
    ['/signup', '/signin', '/create-event'].includes(location.pathname);


  return (
    <>
      {!hideLayout && <Navbar />}
      {children}
      {!hideLayout && <Footer />}
    </>
  );
}

function App() {
  return (
    <Router>
      <LayoutWrapper>
        <Routes>
          {/* Public pages */}
          <Route path="/" element={<Home />} />
          <Route path="/events" element={<UpcomingEvents />} />
          <Route path="/past-events" element={<PastEvents />} />
          <Route path="/about" element={<About />} />
          <Route path="/signup" element={<SignUp />} />
          <Route path="/signin" element={<SignIn />} />
          <Route path="/event/:id" element={<EventDetails />} />
          <Route path="/create-event" element={<CreateEvent />} />
          <Route path="/store" element={<Store />} />


          {/* ✅ Admin pages */}
          <Route path="/admin/dashboard" element={<AdminDashboard />} />
          <Route path="/admin/events" element={<AdminAllEvents />} /> {/* 👈 NEW */}
          <Route path="/admin/approvals" element={<AdminEventApproval />} />
          <Route path="/admin/users" element={<AdminUsers />} />
          <Route path="/admin/societies" element={<AdminSocieties />} />
          <Route path="/admin/login" element={<AdminLogin />} />
          <Route path="/admin/change-password" element={<AdminChangePassword />} />
          <Route path="/admin/store" element={<AdminStore />} />




        </Routes>
      </LayoutWrapper>
    </Router>
  );
}

export default App;
