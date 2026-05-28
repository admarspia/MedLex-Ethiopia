import { useState, useEffect } from 'react';
import { useSearchParams, Link } from 'react-router-dom';
import { searchMedicines } from '../services/medicineService';
import { Search, Package, AlertCircle, MapPin } from 'lucide-react';

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
      // Handle different response structures
      let medicinesArray = [];
      
      // Check if result.data has pharmacies directly (single medicine response)
      if (result.data.pharmacies !== undefined) {
        // This is a single medicine response with pharmacies array
        const medicineObj = {
          id: result.data.medicine?.id || result.data.id,
          generic_name: result.data.medicine?.generic_name || result.data.generic_name,
          brand_name: result.data.medicine?.brand_name || result.data.brand_name,
          manufacturer: result.data.medicine?.manufacturer || result.data.manufacturer,
          pharmacies: result.data.pharmacies?.data || result.data.pharmacies || []
        };
        medicinesArray = [medicineObj];
      } 
      // Check if result.data is an array of medicines
      else if (Array.isArray(result.data)) {
        medicinesArray = result.data;
      }
      // Check if result.data has medicine property
      else if (result.data.medicine) {
        const medicineObj = {
          ...result.data.medicine,
          pharmacies: result.data.pharmacies?.data || result.data.pharmacies || []
        };
        medicinesArray = [medicineObj];
      }
      // Check if result.data itself is a medicine object
      else if (result.data.id || result.data.generic_name) {
        medicinesArray = [{
          ...result.data,
          pharmacies: result.data.pharmacies?.data || result.data.pharmacies || []
        }];
      }
      else {
        medicinesArray = [];
      }
      
      setMedicines(medicinesArray);
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
        <h1 style={{ fontSize: '2.5rem', marginBottom: '0.5rem' }}>
          Medical <span style={{ color: 'var(--color-primary)' }}>Inventory</span>
        </h1>
        <p style={{ color: 'var(--text-muted)' }}>
          Search through our comprehensive database of verified medicines
        </p>
      </div>

      <form onSubmit={handleSearch} className="search-container" style={{ marginBottom: '3rem' }}>
        <input
          type="text"
          placeholder="Search by generic name, brand name..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <button type="submit">
          <Search size={18} /> Search
        </button>
      </form>

      {loading && (
        <div style={{ textAlign: 'center', padding: '4rem' }}>
          <div className="spinner" style={{ 
            width: '40px', 
            height: '40px', 
            border: '3px solid var(--color-primary-light)', 
            borderTopColor: 'var(--color-primary)', 
            borderRadius: '50%', 
            animation: 'spin 1s linear infinite',
            margin: '0 auto 1rem'
          }} />
          <p>Searching medicines...</p>
        </div>
      )}

      {error && (
        <div style={{ textAlign: 'center', padding: '4rem', color: 'var(--color-primary)' }}>
          <AlertCircle size={48} style={{ marginBottom: '1rem', opacity: 0.5 }} />
          <p>{error}</p>
        </div>
      )}

      {!loading && !error && medicines.length === 0 && query && (
        <div style={{ textAlign: 'center', padding: '4rem' }}>
          <Package size={48} style={{ opacity: 0.3, marginBottom: '1rem' }} />
          <p>No medicines found for "{query}"</p>
          <button 
            onClick={() => fetchMedicines('paracetamol')}
            className="btn btn-outline" 
            style={{ marginTop: '1rem' }}
          >
            Browse Popular Medicines
          </button>
        </div>
      )}

      {!loading && !error && medicines.length > 0 && (
        <div style={{ 
          display: 'grid', 
          gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', 
          gap: '1.5rem' 
        }}>
          {medicines.map((medicine, index) => {
            // Get pharmacies array - could be direct or nested
            const pharmacies = medicine.pharmacies?.data || medicine.pharmacies || [];
            const hasStock = pharmacies && pharmacies.length > 0;
            const providerCount = pharmacies.length;
            
            return (
              <Link 
                key={medicine.id || index} 
                to={`/medicines/${medicine.id}`} 
                style={{ textDecoration: 'none', color: 'inherit' }}
              >
                <div className="card" style={{ padding: '1.5rem', height: '100%', cursor: 'pointer' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '1rem' }}>
                    <div style={{ 
                      width: '48px', 
                      height: '48px', 
                      borderRadius: '12px', 
                      background: 'var(--color-primary-light)', 
                      display: 'flex', 
                      alignItems: 'center', 
                      justifyContent: 'center' 
                    }}>
                      <Package size={24} color="var(--color-primary)" />
                    </div>
                    <span className={`badge ${hasStock ? 'badge-red' : 'badge-outline'}`} style={{ fontSize: '0.7rem' }}>
                      {hasStock ? 'IN STOCK' : 'DETAILS AVAILABLE'}
                    </span>
                  </div>
                  
                  <h3 style={{ 
                    fontSize: '1.25rem', 
                    marginBottom: '0.5rem',
                    textTransform: 'uppercase',
                    fontWeight: 800
                  }}>
                    {medicine.generic_name}
                  </h3>
                  
                  <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '1rem' }}>
                    {medicine.brand_name || 'Generic Formulation'}
                  </p>
                  
                  {medicine.manufacturer && (
                    <p style={{ 
                      fontSize: '0.75rem', 
                      color: 'var(--text-muted)', 
                      marginBottom: '1rem' 
                    }}>
                      {medicine.manufacturer}
                    </p>
                  )}
                  
                  <div style={{ 
                    display: 'flex', 
                    justifyContent: 'space-between', 
                    alignItems: 'center', 
                    paddingTop: '1rem', 
                    borderTop: '1px solid #eee' 
                  }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                      <MapPin size={12} color="var(--text-muted)" />
                      <span style={{ fontSize: '0.75rem', fontWeight: 600, color: 'var(--text-muted)' }}>
                        {providerCount} Provider{providerCount !== 1 ? 's' : ''}
                      </span>
                    </div>
                    <span style={{ color: 'var(--color-primary)', fontWeight: 800, fontSize: '0.85rem' }}>
                      View Details →
                    </span>
                  </div>
                </div>
              </Link>
            );
          })}
        </div>
      )}
    </div>
  );
}

export default Medicines;
