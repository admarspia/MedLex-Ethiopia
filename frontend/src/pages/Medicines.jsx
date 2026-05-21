import { useState, useEffect } from 'react';
import { useSearchParams, Link } from 'react-router-dom';
import { searchMedicines } from '../services/medicineService';
import { Search, Package, AlertCircle } from 'lucide-react';

function Medicines() {
  const [searchParams, setSearchParams] = useSearchParams();
  const query = searchParams.get('q') || '';
  
  const [medicines, setMedicines] = useState([]);
  const [search, setSearch] = useState(query);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (query) {
      fetchMedicines(query);
    }
  }, [query]);

  const fetchMedicines = async (term) => {
    setLoading(true);
    setError('');
    const result = await searchMedicines(term);
    
    if (result.success && result.data) {
      const medicineData = result.data.medicine || result.data;
      if (result.data.detail){
        console.log(result.data.pharmacies)
      }
      setMedicines(Array.isArray(medicineData) ? medicineData : [medicineData]);
    } else if (result.message) {
      setError(result.message);
      setMedicines([]);
    } else {
      setMedicines([]);
    }
    setLoading(false);
  };

  const handleSearch = (e) => {
    e.preventDefault();
    if (search.trim()) {
      setSearchParams({ q: search });
      fetchMedicines(search);
    }
  };

  return (
    <div className="container" style={{ padding: '4rem 0' }}>
      <div style={{ textAlign: 'center', marginBottom: '3rem' }}>
        <h1 style={{ fontSize: '2.5rem', marginBottom: '0.5rem' }}>Medical <span style={{ color: 'var(--color-primary)' }}>Inventory</span></h1>
        <p style={{ color: 'var(--text-muted)' }}>Search through our comprehensive database of verified medicines</p>
      </div>

      <form onSubmit={handleSearch} className="search-container" style={{ marginBottom: '3rem' }}>
        <input
          type="text"
          placeholder="Search by generic name, brand name..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <button type="submit"><Search size={18} /> Search</button>
      </form>

      {loading && (
        <div style={{ textAlign: 'center', padding: '4rem' }}>Loading medicines...</div>
      )}

      {error && (
        <div style={{ textAlign: 'center', padding: '4rem', color: 'var(--color-primary)' }}>
          <AlertCircle size={48} style={{ marginBottom: '1rem', opacity: 0.5 }} />
          <p>{error}</p>
        </div>
      )}

      {!loading && !error && medicines.length === 0 && query && (
        <div style={{ textAlign: 'center', padding: '4rem' }}>
          <p>No medicines found for "{query}"</p>
        </div>
      )}

      {!loading && !error && medicines.length > 0 && (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(350px, 1fr))', gap: '2rem' }}>
          {medicines.map((medicine) => (
            <Link 
              key={medicine.id || medicine.generic_name} 
              to={`/medicines/${medicine.id}`} 
              style={{ textDecoration: 'none', color: 'inherit' }}
            >
              <div className="card" style={{ padding: '1.5rem', height: '100%', cursor: 'pointer' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '1rem' }}>
                  <div style={{ width: '48px', height: '48px', borderRadius: '12px', background: 'var(--color-primary-light)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <Package size={24} color="var(--color-primary)" />
                  </div>
                  <span className="badge badge-red" style={{ fontSize: '0.7rem' }}>
                    {medicine.pharmacies?.length > 0 ? 'IN STOCK' : 'DETAILS AVAILABLE'}
                  </span>
                </div>
                <h3 style={{ fontSize: '1.25rem', marginBottom: '0.5rem' }}>{medicine.generic_name}</h3>
                <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '1rem' }}>
                  {medicine.brand_name || 'Generic Formulation'}
                </p>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', paddingTop: '1rem', borderTop: '1px solid #eee' }}>
                  <span style={{ fontSize: '0.8rem', fontWeight: 600 }}>
                    {medicine.pharmacies?.length || 0} Provider{(medicine.pharmacies?.length || 0) !== 1 ? 's' : ''}
                  </span>
                  <span style={{ color: 'var(--color-primary)', fontWeight: 800, fontSize: '0.85rem' }}>View Details →</span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}

export default Medicines;
