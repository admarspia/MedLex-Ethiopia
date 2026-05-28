import { useState, useEffect } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { createReservation } from '../services/reservationService';
import { useAuth } from '../context/AuthContext';

function ReserveMedicine() {
    const { id } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    const { user } = useAuth();
    
    const [quantity, setQuantity] = useState(1);
    const [loading, setLoading] = useState(false);
    const [pharmacyId, setPharmacyId] = useState(null);
    const [medicineName, setMedicineName] = useState('');
    const [email, setEmail] = useState('');
    const [prescription, setPrescription] = useState(null);

    useEffect(() => {
        // Get data from location state or URL params
        const state = location.state || {};
        setPharmacyId(state.pharmacy?.id || state.pharmacyId || id);
        setMedicineName(state.medicine?.generic_name || state.medicineName || '');
        
        if (user?.email) {
            setEmail(user.email);
        }
    }, [location, user, id]);

    const handleReserve = async (e) => {
        e.preventDefault();
        
        if (!pharmacyId) {
            alert('Pharmacy information is missing');
            return;
        }
        
        if (!email) {
            alert('Please enter your email address');
            return;
        }
        
        setLoading(true);
        
        try {
            const formData = new FormData();
            formData.append('pharmacy_id', pharmacyId);
            formData.append('reserver_email', email);
            formData.append('generic_name', medicineName);
            formData.append('quantity', quantity);
            
            if (prescription) {
                formData.append('prescription', prescription);
            }
            
            const result = await createReservation(formData);
            
            if (result.success) {
                alert(result.data || 'Reservation created successfully!');
                navigate('/my-reservations');
            } else {
                alert(result.data || result.message || 'Reservation failed');
            }
        } catch (err) {
            console.error(err);
            alert('Reservation failed. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container" style={{ padding: '4rem 0' }}>
            <div className="card" style={{ maxWidth: '500px', margin: '0 auto' }}>
                <h2 style={{ marginBottom: '1.5rem' }}>Reserve Medicine</h2>
                
                <form onSubmit={handleReserve}>
                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>
                            Medicine
                        </label>
                        <input
                            type="text"
                            value={medicineName}
                            disabled
                            className="search-input"
                            style={{ width: '100%', background: '#f5f5f5' }}
                        />
                    </div>
                    
                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>
                            Your Email
                        </label>
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                            className="search-input"
                            style={{ width: '100%' }}
                            placeholder="Enter your email address"
                        />
                    </div>
                    
                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>
                            Quantity
                        </label>
                        <input
                            type="number"
                            min="1"
                            max="100"
                            value={quantity}
                            onChange={(e) => setQuantity(parseInt(e.target.value))}
                            required
                            className="search-input"
                            style={{ width: '100%' }}
                        />
                    </div>
                    
                    <div style={{ marginBottom: '1.5rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>
                            Prescription (Optional)
                        </label>
                        <input
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            onChange={(e) => setPrescription(e.target.files[0])}
                            className="search-input"
                            style={{ width: '100%' }}
                        />
                        <small style={{ color: 'var(--text-muted)' }}>
                            Upload a prescription if required (PDF, JPG, PNG - max 5MB)
                        </small>
                    </div>
                    
                    <button
                        type="submit"
                        disabled={loading}
                        className="btn btn-primary"
                        style={{ width: '100%' }}
                    >
                        {loading ? 'Processing...' : 'Confirm Reservation'}
                    </button>
                </form>
            </div>
        </div>
    );
}

export default ReserveMedicine;
