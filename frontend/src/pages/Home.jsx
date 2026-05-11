import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Search, ShieldCheck, Zap, Globe, Pill, ArrowRight, Activity, PhoneCall, CheckCircle } from 'lucide-react';

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
      <div className="hero" style={{ padding: '8rem 0 12rem' }}>
        <div className="container" style={{ position: 'relative' }}>
          <div style={{ position: 'absolute', top: '-100px', right: '-100px', width: '400px', height: '400px', background: 'var(--color-primary)', opacity: 0.05, filter: 'blur(80px)', borderRadius: '50%', zIndex: 0 }}></div>
          <div style={{ position: 'relative', zIndex: 1 }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', background: 'rgba(239, 68, 68, 0.1)', color: 'var(--color-primary)', padding: '0.6rem 1.25rem', borderRadius: '100px', fontSize: '0.8rem', fontWeight: 900, marginBottom: '2.5rem', textTransform: 'uppercase', letterSpacing: '0.1em' }}>
              <Activity size={18} /> THE ETHIOPIAN HEALTH NETWORK
            </div>
            <h1 style={{ fontSize: '5rem', lineHeight: 0.9, marginBottom: '2rem' }}>Precision Healthcare <br /><span>Discovery</span></h1>
            <p style={{ fontSize: '1.4rem', maxWidth: '650px', marginBottom: '3.5rem', opacity: 0.8 }}>The most advanced platform connecting verified pharmacies and patients across Ethiopia. Find your medication with absolute certainty.</p>

            <form onSubmit={handleSearch} className="search-container" style={{ maxWidth: '800px', boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.15)' }}>
              <input
                type="text"
                placeholder="Search for generic or brand names (e.g. Insulin)..."
                style={{ fontSize: '1.1rem' }}
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
              <button type="submit" style={{ gap: '0.75rem', width: '180px', fontSize: '1.1rem' }}><Search size={22} /> Find Now</button>
            </form>
          </div>
        </div>
      </div>

      {/* Stats Section */}
      <div className="container" style={{ marginTop: '-6rem', position: 'relative', zIndex: 2 }}>
        <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '2rem' }}>
          <div className="card glass-panel" style={{ padding: '3rem', textAlign: 'center', borderBottom: '4px solid var(--color-primary)' }}>
            <h2 style={{ fontSize: '3.5rem', marginBottom: '0.5rem', color: '#000' }}>450+</h2>
            <p style={{ color: 'var(--text-muted)', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em' }}>Verified Pharmacies</p>
          </div>
          <div className="card glass-panel" style={{ padding: '3rem', textAlign: 'center', background: '#000', color: '#fff', border: 'none' }}>
            <h2 style={{ fontSize: '3.5rem', marginBottom: '0.5rem', color: 'var(--color-primary)' }}>12K+</h2>
            <p style={{ color: 'rgba(255,255,255,0.5)', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em' }}>Medications Indexed</p>
          </div>
          <div className="card glass-panel" style={{ padding: '3rem', textAlign: 'center', borderBottom: '4px solid #000' }}>
            <h2 style={{ fontSize: '3.5rem', marginBottom: '0.5rem', color: '#000' }}>98%</h2>
            <p style={{ color: 'var(--text-muted)', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em' }}>Discovery Accuracy</p>
          </div>
        </div>
      </div>

      {/* Featured Medications */}
      <div className="container" style={{ padding: '10rem 0' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: '4rem' }}>
          <div>
            <h2 style={{ fontSize: '3rem', marginBottom: '1rem' }}>Commonly <span>Searched</span></h2>
            <p style={{ color: 'var(--text-muted)' }}>Quick access to common medical records in our database.</p>
          </div>
          <Link to="/medicines" style={{ textDecoration: 'none', color: 'var(--color-primary)', fontWeight: 800, display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            BROWSE FULL DATABASE <ArrowRight size={20} />
          </Link>
        </div>
        <div className="grid">
          {featuredMeds.map((med, i) => (
            <div key={i} className="card glass-panel hover-scale" style={{ padding: '2.5rem' }}>
              <Pill size={32} color="var(--color-primary)" style={{ marginBottom: '1.5rem' }} />
              <h3 style={{ fontSize: '1.5rem', marginBottom: '0.5rem' }}>{med.name}</h3>
              <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '1rem' }}>{med.type}</p>
              <div style={{ background: 'rgba(0,0,0,0.03)', padding: '0.75rem', borderRadius: '8px', fontSize: '0.85rem', fontWeight: 600 }}>
                Primary Use: {med.usage}
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* How it Works Section */}
      <div style={{ background: '#000', color: '#fff', padding: '10rem 0' }}>
        <div className="container">
          <div style={{ textAlign: 'center', marginBottom: '6rem' }}>
            <h2 style={{ fontSize: '3.5rem', color: '#fff', marginBottom: '1.5rem' }}>The Search <span>Protocol</span></h2>
            <p style={{ opacity: 0.6, fontSize: '1.2rem' }}>How MedLex Ethiopia handles your medical inquiries.</p>
          </div>
          <div className="grid">
            {[
              { step: "01", title: "Search Query", desc: "Input generic or brand names into our high-speed lookup engine." },
              { step: "02", title: "Global Sync", desc: "Our system synchronizes with certified pharmacies across the region." },
              { step: "03", title: "Provider Match", desc: "Get instant maps and contact data for providers with live stock." }
            ].map((item, i) => (
              <div key={i} style={{ padding: '2rem' }}>
                <h4 style={{ color: 'var(--color-primary)', fontSize: '1rem', fontWeight: 900, marginBottom: '1rem' }}>STEP {item.step}</h4>
                <h3 style={{ fontSize: '2rem', marginBottom: '1.5rem', color: '#fff' }}>{item.title}</h3>
                <p style={{ opacity: 0.6, lineHeight: '1.8' }}>{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Emergency / Support Section */}
      <div className="container" style={{ padding: '10rem 0' }}>
        <div className="glass-panel" style={{ background: 'var(--color-primary)', color: '#fff', border: 'none', padding: '6rem', borderRadius: '40px', display: 'flex', alignItems: 'center', gap: '4rem', flexWrap: 'wrap' }}>
          <div style={{ flex: 1, minWidth: '300px' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '2rem' }}>
              <PhoneCall size={48} />
              <h2 style={{ fontSize: '2.5rem', margin: 0 }}>Emergency Support</h2>
            </div>
            <p style={{ fontSize: '1.25rem', opacity: 0.9, marginBottom: '3rem' }}>Unable to find a critical life-saving medication? Our human support team can assist in emergency medical logistics.</p>
            <div style={{ display: 'flex', gap: '2rem' }}>
              <div>
                <h4 style={{ margin: '0 0 0.5rem', opacity: 0.7 }}>HOTLINE</h4>
                <p style={{ fontSize: '1.5rem', fontWeight: 900 }}>8877</p>
              </div>
              <div>
                <h4 style={{ margin: '0 0 0.5rem', opacity: 0.7 }}>EMAIL</h4>
                <p style={{ fontSize: '1.5rem', fontWeight: 900 }}>SOS@MEDLEX.ET</p>
              </div>
            </div>
          </div>
          <div style={{ flex: 1, minWidth: '300px', background: 'rgba(0,0,0,0.1)', padding: '4rem', borderRadius: '30px' }}>
            <h3 style={{ marginBottom: '2rem' }}>Verified Quality</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '1.5rem' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}><CheckCircle size={20} /> EFDA Licensed Providers</div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}><CheckCircle size={20} /> Genuine Pharmaceutical Data</div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}><CheckCircle size={20} /> Secure Patient Privacy</div>
            </div>
          </div>
        </div>
      </div>

      {/* Final CTA */}
      <div className="container" style={{ padding: '12rem 0', textAlign: 'center' }}>
        <h2 style={{ fontSize: '4.5rem', marginBottom: '2rem', lineHeight: 1 }}>Join the <span>Network</span></h2>
        <p style={{ color: 'var(--text-muted)', fontSize: '1.25rem', marginBottom: '4rem', maxWidth: '700px', margin: '0 auto 4rem' }}>Digitize your pharmacy operations and join Ethiopia's most trusted healthcare ecosystem today.</p>
        <div style={{ display: 'flex', gap: '1.5rem', justifyContent: 'center' }}>
          <Link to="/register" className="btn btn-primary" style={{ padding: '1.5rem 4rem', fontSize: '1.2rem' }}>Become a Provider</Link>
          <Link to="/about" className="btn btn-outline" style={{ padding: '1.5rem 4rem', fontSize: '1.2rem' }}>Learn More</Link>
        </div>
      </div>
    </div>
  );
}

export default Home;
