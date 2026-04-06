import { Link } from 'react-router-dom';

function Home() {
  return (
    <div className="hero">
      <div className="hero-glow-1"></div>
      <div className="container">
        <h1>Welcome to <span>MedLex</span></h1>
        <p>Connecting pharmacies to streamline the health network in Ethiopia.</p>
        <Link to="/pharmacy-dashboard" className="btn btn-primary">Go to Dashboard</Link>
      </div>
    </div>
  );
}

export default Home;
