import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Search, Pill, Activity, PhoneCall, CheckCircle, ArrowRight } from 'lucide-react';

function Home() {
  const navigate = useNavigate();
  const [searchTerm, setSearchTerm] = useState('');

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchTerm.trim()) {
      navigate(`/medicines?q=${encodeURIComponent(searchTerm)}`);
    }
  };

  const featuredMeds = [
    { name: 'Paracetamol', type: 'Analgesic', usage: 'Pain & Fever' },
    { name: 'Amoxicillin', type: 'Antibiotic', usage: 'Infections' },
    { name: 'Metformin', type: 'Antidiabetic', usage: 'Blood Sugar' },
    { name: 'Omeprazole', type: 'Antiulcer', usage: 'Acid Reflux' }
  ];

  return (
    <div className="animate-in">
      {/* Hero Section */}
      <div className="hero" style={{ padding: '8rem 0 12rem', background: 'linear-gradient(135deg, #fff 0%, #f5f5f5 100%)' }}>
        <div className="container">
          <div style={{ position: 'relative', zIndex: 1 }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', background: 'rgba(239, 68, 68, 0.1)', color: 'var(--color-primary)', padding: '0.6rem 1.25rem', borderRadius: '100px', fontSize: '0.8rem', fontWeight: 900, marginBottom: '2.5rem' }}>
              <Activity size={18} /> THE ETHIOPIAN HEALTH NETWORK
            </div>
            <h1 style={{ fontSize: '4rem', lineHeight: 1.1, marginBottom: '2rem' }}>Precision Healthcare <br /><span style={{ color: 'var(--color-primary)' }}>Discovery</span></h1>
            <p style={{ fontSize: '1.2rem', maxWidth: '600px', marginBottom: '3rem', opacity: 0.8 }}>The most advanced platform connecting verified pharmacies and patients across Ethiopia.</p>

            <form onSubmit={handleSearch} className="search-container" style={{ maxWidth: '700px' }}>
              <input
                type="text"
                placeholder="Search for generic or brand names (e.g., Paracetamol)..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
              <button type="submit"><Search size={20} /> Find Now</button>
            </form>
          </div>
        </div>
      </div>

      {/* Stats Section */}
      <div className="container" style={{ marginTop: '-4rem', position: 'relative', zIndex: 2 }}>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '2rem' }}>
          <div className="card" style={{ padding: '2rem', textAlign: 'center', background: '#fff' }}>
            <h2 style={{ fontSize: '3rem', color: 'var(--color-primary)' }}>450+</h2>
            <p style={{ fontWeight: 700 }}>Verified Pharmacies</p>
          </div>
          <div className="card" style={{ padding: '2rem', textAlign: 'center', background: '#000', color: '#fff' }}>
            <h2 style={{ fontSize: '3rem', color: 'var(--color-primary)' }}>12K+</h2>
            <p style={{ opacity: 0.7 }}>Medications Indexed</p>
          </div>
          <div className="card" style={{ padding: '2rem', textAlign: 'center', background: '#fff' }}>
            <h2 style={{ fontSize: '3rem', color: 'var(--color-primary)' }}>98%</h2>
            <p style={{ fontWeight: 700 }}>Discovery Accuracy</p>
          </div>
        </div>
      </div>

      {/* Featured Medications */}
      <div className="container" style={{ padding: '8rem 0' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: '3rem', flexWrap: 'wrap', gap: '1rem' }}>
          <div>
            <h2 style={{ fontSize: '2.5rem', marginBottom: '0.5rem' }}>Commonly <span style={{ color: 'var(--color-primary)' }}>Searched</span></h2>
            <p style={{ color: 'var(--text-muted)' }}>Quick access to common medical records in our database.</p>
          </div>
          <Link to="/medicines" style={{ color: 'var(--color-primary)', fontWeight: 800, textDecoration: 'none', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            BROWSE FULL DATABASE <ArrowRight size={18} />
          </Link>
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '2rem' }}>
          {featuredMeds.map((med, i) => (
            <div key={i} className="card" style={{ padding: '2rem' }}>
              <Pill size={32} color="var(--color-primary)" style={{ marginBottom: '1rem' }} />
              <h3 style={{ fontSize: '1.3rem', marginBottom: '0.5rem' }}>{med.name}</h3>
              <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '1rem' }}>{med.type}</p>
              <div style={{ background: '#f5f5f5', padding: '0.75rem', borderRadius: '8px', fontSize: '0.85rem', fontWeight: 600 }}>
                Primary Use: {med.usage}
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* How it Works */}
      <div style={{ background: '#000', color: '#fff', padding: '8rem 0' }}>
        <div className="container">
          <div style={{ textAlign: 'center', marginBottom: '4rem' }}>
            <h2 style={{ fontSize: '2.5rem', marginBottom: '1rem' }}>The Search <span style={{ color: 'var(--color-primary)' }}>Protocol</span></h2>
            <p style={{ opacity: 0.6 }}>How MedLex Ethiopia handles your medical inquiries.</p>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '3rem' }}>
            {[
              { step: "01", title: "Search Query", desc: "Input generic or brand names into our high-speed lookup engine." },
              { step: "02", title: "Global Sync", desc: "Our system synchronizes with certified pharmacies across the region." },
              { step: "03", title: "Provider Match", desc: "Get instant contact data for providers with live stock." }
            ].map((item, i) => (
              <div key={i}>
                <h4 style={{ color: 'var(--color-primary)', fontSize: '1rem', marginBottom: '1rem' }}>STEP {item.step}</h4>
                <h3 style={{ fontSize: '1.5rem', marginBottom: '1rem' }}>{item.title}</h3>
                <p style={{ opacity: 0.6 }}>{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="container" style={{ padding: '8rem 0', textAlign: 'center' }}>
        <h2 style={{ fontSize: '3rem', marginBottom: '1rem' }}>Join the <span style={{ color: 'var(--color-primary)' }}>Network</span></h2>
        <p style={{ color: 'var(--text-muted)', marginBottom: '2rem', maxWidth: '600px', margin: '0 auto 2rem' }}>
          Digitize your pharmacy operations and join Ethiopia's most trusted healthcare ecosystem.
        </p>
        <div style={{ display: 'flex', gap: '1rem', justifyContent: 'center', flexWrap: 'wrap' }}>
          <Link to="/register" className="btn btn-primary" style={{ padding: '1rem 2rem' }}>Become a Provider</Link>
          <Link to="/about" className="btn btn-outline" style={{ padding: '1rem 2rem' }}>Learn More</Link>
        </div>
      </div>
    </div>
  );
}

export default Home;
