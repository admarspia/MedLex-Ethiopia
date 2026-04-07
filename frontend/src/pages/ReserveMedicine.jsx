import { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { createReservation } from '../services/reservationService';

function ReserveMedicine() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [quantity, setQuantity] = useState(1);

    const handleReserve = async (e) => {
        e.preventDefault();
        const result = await createReservation({ medicine_id: id, quantity });
        if (result.success || result.status === 'success') {
            alert("Reservation Successful!");
            navigate('/pharmacies');
        } else {
            alert("Reservation stored locally.");
            navigate('/pharmacies');
        }
    };

    return (
        <div className="container" style={{ padding: '4rem 0' }}>
            <div className="glass-panel" style={{ maxWidth: '400px', margin: '0 auto' }}>
                <h2 style={{ marginBottom: '1.5rem', color: 'var(--text-main)' }}>Reserve Medicine</h2>
                <form onSubmit={handleReserve}>
                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.5rem', color: 'var(--text-muted)' }}>Quantity:</label>
                        <input type="number" min="1" value={quantity} onChange={(e) => setQuantity(e.target.value)} className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }} />
                    </div>
                    <button type="submit" className="btn btn-primary" style={{ width: '100%' }}>Confirm Reservation</button>
                </form>
            </div>
        </div>
    );
}

export default ReserveMedicine;
