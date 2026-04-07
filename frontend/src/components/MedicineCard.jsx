import { Link } from 'react-router-dom';

function MedicineCard({ medicine }) {
    return (
        <div className="card">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                <h3 className="card-title">{medicine.generic_name}</h3>
                <span className="badge badge-green">In Stock</span>
            </div>
            <p className="card-subtitle">{medicine.brand_names}</p>
            <div className="card-content" style={{ marginBottom: '1.5rem' }}>
                <p>{medicine.details || 'No details available.'}</p>
            </div>
            <div style={{ display: 'flex', gap: '0.5rem', marginTop: 'auto' }}>
                <Link to={`/medicine/${medicine.id}`} className="btn btn-outline" style={{ flex: 1, padding: '0.6rem', textAlign: 'center' }}>Details</Link>
                <Link to={`/reserve/${medicine.id}`} className="btn btn-primary" style={{ flex: 1, padding: '0.6rem', textAlign: 'center' }}>Reserve</Link>
            </div>
        </div>
    );
}

export default MedicineCard;
