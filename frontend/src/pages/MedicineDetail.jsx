import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { getMedicineById } from '../services/medicineService';
import { ArrowLeft, Pill, AlertTriangle, CheckCircle, MapPin, Loader, Info } from 'lucide-react';
import { useAuth } from '../context/AuthContext';

function MedicineDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [medicine, setMedicine] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [selectedPharmacy, setSelectedPharmacy] = useState(null);
  const [quantity, setQuantity] = useState(1);

  useEffect(() => {
    fetchMedicine();
  }, [id]);

  const fetchMedicine = async () => {
    setLoading(true);
    const result = await getMedicineById(id);
    
    if (result.success && result.data) {
      setMedicine(result.data);
    } else {
      setError(result.message || 'Medicine not found');
    }
    setLoading(false);
  };

  const handleReserve = async (pharmacy) => {
    if (!user) {
      navigate('/login');
      return;
    }
    
    const formData = new FormData();
    formData.append('pharmacy_id', pharmacy.id);
    formData.append('generic_name', medicine.medicine?.generic_name);
    formData.append('quantity', quantity);
    formData.append('reserver_email', user.email || 'user@example.com');
    
    // You would need to add prescription file upload here
    navigate(`/reservation/create/${pharmacy.id}`, { 
      state: { medicine: medicine.medicine, pharmacy, quantity } 
    });
  };

  if (loading) {
    return (
      <div className="container" style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '60vh' }}>
        <Loader size={48} className="spinner" style={{ animation: 'spin 1s linear infinite', color: 'var(--color-primary)' }} />
      </div>
    );
  }

  if (error || !medicine) {
    return (
      <div className="container" style={{ padding: '4rem 0', textAlign: 'center' }}>
        <h2>{error || 'Medicine not found'}</h2>
        <Link to="/medicines" className="btn btn-primary" style={{ marginTop: '2rem' }}>Back to Search</Link>
      </div>
    );
  }

  const medData = medicine.medicine || medicine;
  const details = medicine.detail;

  return (
    <div className="container" style={{ padding: '4rem 0' }}>
      <Link to="/medicines" style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', marginBottom: '2rem', color: 'var(--text-muted)', textDecoration: 'none' }}>
        <ArrowLeft size={18} /> BACK TO SEARCH
      </Link>

      {/* Medicine Header */}
      <div className="card" style={{ padding: '2rem', marginBottom: '2rem' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '1.5rem' }}>
          <div style={{ width: '64px', height: '64px', borderRadius: '16px', background: 'var(--color-primary-light)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <Pill size={32} color="var(--color-primary)" />
          </div>
          <div>
            <h1 style={{ fontSize: '2rem', marginBottom: '0.25rem' }}>{medData.generic_name}</h1>
            {medData.brand_name && <p style={{ color: 'var(--text-muted)' }}>Brand: {medData.brand_name}</p>}
          </div>
        </div>
        
        {medData.manufacturer && (
          <p style={{ marginBottom: '0.5rem' }}><strong>Manufacturer:</strong> {medData.manufacturer}</p>
        )}
        {medData.drug_class && (
          <p style={{ marginBottom: '0.5rem' }}><strong>Drug Class:</strong> {medData.drug_class}</p>
        )}
        {medData.dosage_form && (
          <p><strong>Dosage Form:</strong> {medData.dosage_form} {medData.strength && `(${medData.strength})`}</p>
        )}
      </div>

      {/* Medicine Details */}
      {details && (
        <div className="card" style={{ padding: '2rem', marginBottom: '2rem' }}>
          <h2 style={{ fontSize: '1.5rem', marginBottom: '1.5rem' }}>Clinical Information</h2>
          
          {details.indications && (
            <div style={{ marginBottom: '1.5rem' }}>
              <h3 style={{ fontSize: '1.1rem', marginBottom: '0.5rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                <Info size={18} /> Indications
              </h3>
              <p style={{ color: 'var(--text-muted)', lineHeight: 1.6 }}>{details.indications || medData.indications}</p>
            </div>
          )}
          
          {details.dosage_and_administration && (
            <div style={{ marginBottom: '1.5rem' }}>
              <h3 style={{ fontSize: '1.1rem', marginBottom: '0.5rem' }}>Dosage & Administration</h3>
              <p style={{ color: 'var(--text-muted)', lineHeight: 1.6 }}>{details.dosage_and_administration}</p>
            </div>
          )}
          
          {details.contraindications && (
            <div style={{ marginBottom: '1.5rem' }}>
              <h3 style={{ fontSize: '1.1rem', marginBottom: '0.5rem', display: 'flex', alignItems: 'center', gap: '0.5rem', color: 'var(--color-primary)' }}>
                <AlertTriangle size={18} /> Contraindications
              </h3>
              <p style={{ color: 'var(--text-muted)', lineHeight: 1.6 }}>{details.contraindications}</p>
            </div>
          )}
          
          {details.adverse_reactions && (
            <div style={{ marginBottom: '1.5rem' }}>
              <h3 style={{ fontSize: '1.1rem', marginBottom: '0.5rem' }}>Adverse Reactions</h3>
              <p style={{ color: 'var(--text-muted)', lineHeight: 1.6 }}>{details.adverse_reactions}</p>
            </div>
          )}
          
          {details.drug_interactions && (
            <div>
              <h3 style={{ fontSize: '1.1rem', marginBottom: '0.5rem' }}>Drug Interactions</h3>
              <p style={{ color: 'var(--text-muted)', lineHeight: 1.6 }}>{details.drug_interactions}</p>
            </div>
          )}
        </div>
      )}

      {/* Available Pharmacies */}
      {medicine.pharmacies && medicine.pharmacies.length > 0 && (
        <div className="card" style={{ padding: '2rem' }}>
          <h2 style={{ fontSize: '1.5rem', marginBottom: '1rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            <MapPin size={24} /> Available at {medicine.pharmacies.length} Pharmacy{medicine.pharmacies.length !== 1 ? 'ies' : ''}
          </h2>
          
          <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
            {medicine.pharmacies.map((pharmacy, idx) => (
              <div key={idx} style={{ padding: '1.5rem', border: '1px solid #eee', borderRadius: '12px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '1rem' }}>
                  <div>
                    <h3 style={{ marginBottom: '0.25rem' }}>{pharmacy.name}</h3>
                    <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '0.5rem' }}>{pharmacy.address}</p>
                    <p style={{ fontSize: '0.9rem' }}>📞 {pharmacy.phone}</p>
                  </div>
                  <div style={{ textAlign: 'right' }}>
                    <div style={{ fontSize: '1.5rem', fontWeight: 800, color: 'var(--color-primary)' }}>{pharmacy.price} ETB</div>
                    <div style={{ marginTop: '0.5rem' }}>
                      <span className={`badge ${pharmacy.count > 0 ? 'badge-red' : 'badge-outline'}`}>
                        {pharmacy.count > 0 ? `${pharmacy.count} units in stock` : 'Out of stock'}
                      </span>
                    </div>
                    {pharmacy.count > 0 && (
                      <div style={{ marginTop: '1rem', display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                        <input 
                          type="number" 
                          min="1" 
                          max={pharmacy.count}
                          value={selectedPharmacy?.id === pharmacy.id ? quantity : 1}
                          onChange={(e) => {
                            setSelectedPharmacy(pharmacy);
                            setQuantity(parseInt(e.target.value));
                          }}
                          style={{ width: '80px', padding: '0.5rem', borderRadius: '8px', border: '1px solid #ddd' }}
                        />
                        <button 
                          onClick={() => handleReserve(pharmacy)} 
                          className="btn btn-primary"
                          style={{ padding: '0.5rem 1rem', fontSize: '0.85rem' }}
                        >
                          Reserve
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

export default MedicineDetail;
