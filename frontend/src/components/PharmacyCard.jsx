import { Link } from 'react-router-dom';

function PharmacyCard({ pharmacy }) {
    return (
        <div className="card">
            <h3 className="card-title">{pharmacy.name}</h3>
            <p className="card-subtitle">{pharmacy.location}</p>
            <div className="card-content">
                <p><strong>Contact:</strong> {pharmacy.contact}</p>
            </div>
            <div style={{ marginTop: '1.5rem' }}>
                <Link to="/medicines" className="btn btn-outline" style={{ display: 'block', textAlign: 'center' }}>
                    View Medicines
                </Link>
            </div>
        </div>
    );
}

export default PharmacyCard;
