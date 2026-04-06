import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Store, LogOut, PackageSearch, Plus, Activity, RefreshCw, X, Save } from 'lucide-react';

// Mock dashboard data, fallback if backend is down
const initialInventory = [
    { med_id: 1, generic_name: 'Paracetamol', brand_names: 'Panadol', stock_status: 'available', price: '45.00' },
    { med_id: 2, generic_name: 'Amoxicillin', brand_names: 'Amoxil', stock_status: 'low_stock', price: '50.00' }
];

export default function PharmacyDashboard() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const [inventory, setInventory] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [allMedicines, setAllMedicines] = useState([]);

    // Form State
    const [editMode, setEditMode] = useState(false);
    const [formData, setFormData] = useState({
        medicine_id: '',
        stock_status: 'available',
        price: ''
    });

    useEffect(() => {
        if (!user) {
            navigate('/login');
            return;
        }
        fetchInventory();
        fetchAllMedicines();
    }, [user, navigate]);

    const fetchInventory = async () => {
        try {
            setLoading(true);
            const res = await fetch(`http://localhost:8000/api/pharmacy_inventory.php?pharmacy_id=${user.pharmacy_id}`);
            const output = await res.json();
            if (output.status === 'success') {
                setInventory(output.data);
            } else {
                throw new Error('Failed to fetch inventory');
            }
        } catch (err) {
            setInventory(initialInventory); // Fallback
        } finally {
            setLoading(false);
        }
    };

    const fetchAllMedicines = async () => {
        try {
            const res = await fetch(`http://localhost:8000/api/medicines.php`);
            const output = await res.json();
            if (output.status === 'success') {
                setAllMedicines(output.data);
            }
        } catch (err) {
            setAllMedicines([{ id: 1, generic_name: 'Paracetamol' }, { id: 2, generic_name: 'Amoxicillin' }, { id: 3, generic_name: 'Ibuprofen' }]); // Fallback
        }
    };

    if (!user) return null;

    const handleLogout = () => {
        logout();
        navigate('/');
    };

    const getStatusBadgeColor = (status) => {
        if (status === 'available') return 'badge-green';
        if (status === 'low_stock') return 'badge-red';
        return 'badge-red';
    };

    const openAddModal = () => {
        setEditMode(false);
        setFormData({ medicine_id: '', stock_status: 'available', price: '' });
        setIsModalOpen(true);
    };

    const openEditModal = (item) => {
        setEditMode(true);
        setFormData({
            medicine_id: item.med_id,
            stock_status: item.stock_status,
            price: item.price
        });
        setIsModalOpen(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const res = await fetch('http://localhost:8000/api/pharmacy_inventory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    pharmacy_id: user.pharmacy_id,
                    ...formData
                })
            });
            const output = await res.json();
            if (output.status === 'success') {
                setIsModalOpen(false);
                fetchInventory(); // Refresh
            }
        } catch (err) {
            // Simulate frontend update for mock mode
            const medInfo = allMedicines.find(m => m.id == formData.medicine_id);
            const newItem = {
                med_id: formData.medicine_id,
                generic_name: medInfo ? medInfo.generic_name : 'Unknown',
                brand_names: medInfo ? medInfo.brand_names : '-',
                stock_status: formData.stock_status,
                price: formData.price
            };

            let newInventory = [...inventory];
            const existingIdx = newInventory.findIndex(i => i.med_id == formData.medicine_id);

            if (existingIdx >= 0) {
                newInventory[existingIdx] = newItem; // Update
            } else {
                newInventory.push(newItem); // Add
            }

            setInventory(newInventory);
            setIsModalOpen(false);
        }
    };

    return (
        <div className="container animate-in" style={{ padding: '3rem 1.5rem' }}>

            {/* Dashboard Header */}
            <div className="glass-panel" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '1rem', marginBottom: '3rem', padding: '1.5rem 2.5rem' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                    <div style={{ width: '48px', height: '48px', borderRadius: '12px', background: 'rgba(239, 68, 68, 0.1)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--color-primary)' }}>
                        <Store size={24} />
                    </div>
                    <div>
                        <h1 style={{ fontSize: '1.5rem', margin: 0 }}>{user.name || 'Your Pharmacy'}</h1>
                        <p style={{ color: 'var(--color-primary-light)', fontSize: '0.9rem', margin: 0, display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <Activity size={14} /> Active Session
                        </p>
                    </div>
                </div>
                <button onClick={handleLogout} className="btn-outline" style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', padding: '0.6rem 1rem', borderRadius: '8px', border: '1px solid rgba(0,0,0,0.1)', background: 'transparent', color: 'var(--text-muted)', cursor: 'pointer', fontWeight: 600 }}>
                    <LogOut size={16} /> Sign Out
                </button>
            </div>

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem', flexWrap: 'wrap', gap: '1rem' }}>
                <h2 style={{ fontSize: '1.8rem' }}>Inventory Status</h2>
                <button onClick={openAddModal} className="btn btn-primary" style={{ padding: '0.75rem 1.5rem', fontSize: '0.9rem' }}>
                    <Plus size={18} /> Add New Medicine
                </button>
            </div>

            <div className="card" style={{ padding: '0', overflow: 'hidden', position: 'relative' }}>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                        <thead>
                            <tr style={{ borderBottom: '1px solid var(--border-color)', background: 'rgba(0,0,0,0.02)' }}>
                                <th style={{ padding: '1.25rem 1.5rem', color: 'var(--text-muted)', fontWeight: 500, fontSize: '0.9rem' }}>Medicine Name</th>
                                <th style={{ padding: '1.25rem 1.5rem', color: 'var(--text-muted)', fontWeight: 500, fontSize: '0.9rem' }}>Brand</th>
                                <th style={{ padding: '1.25rem 1.5rem', color: 'var(--text-muted)', fontWeight: 500, fontSize: '0.9rem' }}>Stock Status</th>
                                <th style={{ padding: '1.25rem 1.5rem', color: 'var(--text-muted)', fontWeight: 500, fontSize: '0.9rem' }}>Price (Birr)</th>
                                <th style={{ padding: '1.25rem 1.5rem', color: 'var(--text-muted)', fontWeight: 500, fontSize: '0.9rem', textAlign: 'right' }}>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {inventory.map((item, index) => (
                                <tr key={index} style={{ borderBottom: '1px solid rgba(0,0,0,0.05)', transition: 'background 0.2s' }} className="hover-bg-dark">
                                    <td style={{ padding: '1.25rem 1.5rem', fontWeight: 600, color: 'white' }}>{item.generic_name}</td>
                                    <td style={{ padding: '1.25rem 1.5rem', color: 'var(--text-muted)' }}>{item.brand_names || '-'}</td>
                                    <td style={{ padding: '1.25rem 1.5rem' }}>
                                        <span className={`badge ${getStatusBadgeColor(item.stock_status)}`}>
                                            {item.stock_status.replace('_', ' ')}
                                        </span>
                                    </td>
                                    <td style={{ padding: '1.25rem 1.5rem', color: 'var(--text-main)', fontWeight: 600 }}>{item.price}</td>
                                    <td style={{ padding: '1.25rem 1.5rem', textAlign: 'right' }}>
                                        <button onClick={() => openEditModal(item)} style={{ background: 'transparent', border: '1px solid rgba(0,0,0,0.2)', padding: '0.5rem', borderRadius: '8px', color: 'var(--text-main)', cursor: 'pointer', transition: 'all 0.2s' }}>
                                            <RefreshCw size={16} /> Edit
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            {inventory.length === 0 && !loading && (
                                <tr>
                                    <td colSpan="5" style={{ padding: '4rem', textAlign: 'center', color: 'var(--text-muted)' }}>
                                        <PackageSearch size={48} style={{ opacity: 0.3, marginBottom: '1rem', display: 'block', margin: '0 auto' }} />
                                        No inventory found. Start adding medicines to appear in searches.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Adding/Editing Modal */}
            {isModalOpen && (
                <div style={{ position: 'fixed', top: 0, left: 0, right: 0, bottom: 0, background: 'rgba(0,0,0,0.7)', backdropFilter: 'blur(4px)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000, padding: '1.5rem' }}>
                    <div className="card animate-in" style={{ width: '100%', maxWidth: '500px', position: 'relative' }}>
                        <button onClick={() => setIsModalOpen(false)} style={{ position: 'absolute', top: '1.5rem', right: '1.5rem', background: 'none', border: 'none', color: 'var(--text-muted)', cursor: 'pointer' }}>
                            <X size={24} />
                        </button>
                        <h2 style={{ marginBottom: '1.5rem', fontSize: '1.5rem' }}>{editMode ? 'Update Medicine' : 'Add New Medicine'}</h2>

                        <form onSubmit={handleSubmit}>
                            <div style={{ marginBottom: '1.25rem' }}>
                                <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 500, color: 'var(--text-muted)', fontSize: '0.9rem' }}>Select Medicine</label>
                                <select
                                    className="search-input"
                                    value={formData.medicine_id}
                                    onChange={(e) => setFormData({ ...formData, medicine_id: e.target.value })}
                                    required
                                    disabled={editMode}
                                    style={{ width: '100%', padding: '1rem', appearance: 'auto', background: 'rgba(0,0,0,0.03)' }}
                                >
                                    <option value="" disabled>-- Select a medicine from system --</option>
                                    {allMedicines.map(m => (
                                        <option key={m.id} value={m.id} style={{ color: 'black' }}>{m.generic_name}</option>
                                    ))}
                                </select>
                            </div>

                            <div style={{ marginBottom: '1.25rem' }}>
                                <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 500, color: 'var(--text-muted)', fontSize: '0.9rem' }}>Stock Status</label>
                                <select
                                    className="search-input"
                                    value={formData.stock_status}
                                    onChange={(e) => setFormData({ ...formData, stock_status: e.target.value })}
                                    required
                                    style={{ width: '100%', padding: '1rem', appearance: 'auto', background: 'rgba(0,0,0,0.03)' }}
                                >
                                    <option value="available" style={{ color: 'black' }}>Available</option>
                                    <option value="low_stock" style={{ color: 'black' }}>Low Stock</option>
                                    <option value="out_of_stock" style={{ color: 'black' }}>Out of Stock</option>
                                </select>
                            </div>

                            <div style={{ marginBottom: '2rem' }}>
                                <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 500, color: 'var(--text-muted)', fontSize: '0.9rem' }}>Price (Birr)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    className="search-input"
                                    placeholder="e.g. 50.00"
                                    value={formData.price}
                                    onChange={(e) => setFormData({ ...formData, price: e.target.value })}
                                    required
                                    style={{ width: '100%', padding: '1rem' }}
                                />
                            </div>

                            <div style={{ display: 'flex', gap: '1rem', justifyContent: 'flex-end' }}>
                                <button type="button" onClick={() => setIsModalOpen(false)} className="btn-outline" style={{ display: 'inline-flex', padding: '0.8rem 1.5rem', borderRadius: '8px', border: 'none', background: 'transparent', color: 'var(--text-muted)', cursor: 'pointer', fontWeight: 600 }}>Cancel</button>
                                <button type="submit" className="btn btn-primary" style={{ padding: '0.8rem 1.5rem', fontSize: '1rem' }}>
                                    <Save size={18} /> {editMode ? 'Save Changes' : 'Add to Inventory'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
