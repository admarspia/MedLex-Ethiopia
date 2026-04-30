import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Store, LogOut, Activity, Plus, Trash2, X, AlertCircle } from 'lucide-react';
import { getPharmacyInventory } from '../services/pharmacyService';
import { addMedicineToStock, removeMedicineFromStock, updateMedicineStock } from '../services/medicineService';

export default function PharmacyDashboard() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [inventory, setInventory] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const [selectedMedId, setSelectedMedId] = useState(null);
  const [allMedicines, setAllMedicines] = useState([]);
  const [pharmacyId, setPharmacyId] = useState(0);

  const [formData, setFormData] = useState({
    generic_name: '',
    count: '',
    price: ''
  });
  useEffect(() => {
    const checkSession = async () => {
      if (!user) {
        console.log("user not found");

        navigate('/login');
        return;
      }

      console.log("user has logged in");

      try {
        const res = await fetch("http://localhost:8000/get-session", {
          credentials: "include"
        });

        const result = await res.json();

        console.log(result);

        if (result.status == 200) {
          setPharmacyId(result.data);
          console.log("pharmacy_id:", pharmacyId, result.status);

        } else {
          navigate('/login');
        }

      } catch (err) {
        console.error("Session check failed:", err);
        navigate('/login');
      }
    };

    checkSession();
  }, [user, navigate]);

  const fetchAllMedicines = async (pharmacyId) => {
    try {
      const result = await getPharmacyInventory(pharmacyId);
      if (result.status == 200) {
        setInventory(result.data);
      }
    } catch (err) {
      console.error('Failed to fetch medicines', err);
    }
  };
  fetchAllMedicines(pharmacyId);

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  const openAddModal = () => {
    setEditMode(false);
    setSelectedMedId(null);
    setFormData({ generic_name: '', count: '', price: '' });
    setIsModalOpen(true);
  };

  const openEditModal = (item) => {
    setEditMode(true);
    setSelectedMedId(item.med_id);
    setFormData({
      generic_name: item.generic_name,
      count: item.count,
      price: item.price,
      image: null
    });
    setIsModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    let result;
    if (editMode) {
      result = await updateMedicineStock(selectedMedId, formData.count, formData.price, user.token);
    } else {
      const submitData = new FormData();
      submitData.append('generic_name', formData.generic_name);
      submitData.append('count', formData.count);
      submitData.append('price', formData.price);
      submitData.append('image', formData.image); // <-- add this
      result = await addMedicineToStock(submitData, user.token);
    }

    if (result.status == 200) {
      setIsModalOpen(false);
    } else {
      alert(result.message || "Operation failed");
    }
  };

  const handleRemove = async (medId) => {
    if (!confirm("Confirm removal? This will remove the medicine from your public stock list.")) return;
    const result = await removeMedicineFromStock(medId, user.token);
    if (result.success) {
      fetchInventory();
    }
  };

  if (!user) return null;

  return (
    <div className="container animate-in" style={{ padding: '4rem 0' }}>
      <div className="glass-panel" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '4rem', padding: '2rem 3rem' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: '1.5rem' }}>
          <div style={{ width: '56px', height: '56px', borderRadius: '14px', background: '#000', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff' }}>
            <Store size={28} />
          </div>
          <div>
            <h1 style={{ fontSize: '1.8rem', marginBottom: '0.2rem' }}>{user.email.split('@')[0]}</h1>
            <p style={{ color: 'var(--color-primary)', fontWeight: 700, fontSize: '0.85rem', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
              Authorized Pharmacy Portal
            </p>
          </div>
        </div>
        <button onClick={handleLogout} className="btn btn-outline" style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem' }}>
          <LogOut size={16} /> Logout
        </button>
      </div>

      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: '2.5rem' }}>
        <div>
          <h2 style={{ fontSize: '2.5rem', marginBottom: '0.5rem' }}>Inventory <span>Stock</span></h2>
          <p style={{ color: 'var(--text-muted)' }}>Manage your medication availability and pricing</p>
        </div>
        <button onClick={openAddModal} className="btn btn-primary" style={{ padding: '1rem 2rem' }}>
          <Plus size={20} /> Add New Medicine
        </button>
      </div>

      <div className="glass-panel" style={{ padding: '0', overflow: 'hidden' }}>
        <table style={{ minWidth: '100%' }}>
          <thead>
            <tr style={{ background: '#000', color: '#fff' }}>
              <th style={{ color: '#fff', border: 'none' }}>Medicine Name</th>
              <th style={{ color: '#fff', border: 'none' }}>Brand</th>
              <th style={{ color: '#fff', border: 'none' }}>Stock</th>
              <th style={{ color: '#fff', border: 'none' }}>Price (ETB)</th>
              <th style={{ color: '#fff', border: 'none' }}>Action</th>
            </tr>
          </thead>
          <tbody>
            {inventory.map((item, index) => (
              <tr key={index}>
                <td style={{ fontWeight: 700 }}>{item.generic_name}</td>
                <td>{item.brand_name || '-'}</td>
                <td style={{ fontWeight: 600 }}>{item.count} units</td>
                <td style={{ fontWeight: 800, color: 'var(--color-primary)' }}>{item.price}</td>
                <td>
                  <div style={{ display: 'flex', gap: '0.5rem' }}>
                    <button onClick={() => openEditModal(item)} style={{ background: 'transparent', border: 'none', color: '#666', cursor: 'pointer', padding: '0.5rem' }}>
                      <Plus size={18} style={{ transform: 'rotate(45deg)' }} /> {/* Using a Plus rotated as an X, but let's use a real pencil if I had one, oh wait I can use 'Activity' or just text */}
                      Edit
                    </button>
                    <button onClick={() => handleRemove(item.med_id)} style={{ background: 'transparent', border: 'none', color: 'var(--color-primary)', cursor: 'pointer', padding: '0.5rem' }}>
                      <Trash2 size={18} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {inventory.length === 0 && !loading && (
              <tr>
                <td colSpan="5" style={{ padding: '6rem', textAlign: 'center' }}>
                  <Activity size={48} style={{ opacity: 0.1, marginBottom: '1rem' }} />
                  <p style={{ color: 'var(--text-muted)' }}>No medicines currently in stock. Add one to get started.</p>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {isModalOpen && (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.85)', backdropFilter: 'blur(8px)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 2000, padding: '1rem' }}>
          <div className="glass-panel" style={{ width: '100%', maxWidth: '400px', background: '#fff' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
              <h2 style={{ fontSize: '1.5rem' }}>{editMode ? 'Update Stock' : 'Add Medicine'}</h2>
              <button onClick={() => setIsModalOpen(false)} style={{ background: 'transparent', border: 'none', cursor: 'pointer' }}><X size={24} /></button>
            </div>
            <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1.5rem' }}>
              <div>
                <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Medicine Generic Name</label>
                <input
                  list="med-suggestions"
                  className="search-input"
                  value={formData.generic_name}
                  onChange={(e) => setFormData({ ...formData, generic_name: e.target.value })}
                  required
                  disabled={editMode}
                />
                <datalist id="med-suggestions">
                  {allMedicines.map(m => <option key={m.id} value={m.generic_name} />)}
                </datalist>
                {editMode && <p style={{ fontSize: '0.75rem', color: '#999', marginTop: '0.4rem' }}>Generic name cannot be changed during update.</p>}
              </div>
              <div>
                <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>
                  Medicine Image
                </label>
                <input
                  type="file"
                  accept="image/*"
                  className="search-input"
                  onChange={(e) =>
                    setFormData({ ...formData, image: e.target.files[0] })
                  }
                  required={!editMode}
                />
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
                <div>
                  <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Quantity</label>
                                    <input type="number" className="search-input" value={formData.count} onChange={(e) => setFormData({ ...formData, count: e.target.value })} required />
                                </div>
                                <div>
                                    <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Unit Price</label>
                                    <input type="number" step="0.01" className="search-input" value={formData.price} onChange={(e) => setFormData({ ...formData, price: e.target.value })} required />
                                </div>
                            </div>

                            <div style={{ background: 'var(--color-primary-light)', padding: '1rem', borderRadius: '8px', display: 'flex', gap: '0.75rem', alignItems: 'center', color: 'var(--color-primary)', fontSize: '0.8rem' }}>
                                <AlertCircle size={20} />
                                <p>{editMode ? "Updates will reflect immediately in the discovery search results." : "This medication will be visible to all users in the discovery network."}</p>
                            </div>
                            <button type="submit" className="btn btn-primary" style={{ height: '3.5rem' }}>
                                {editMode ? 'Confirm Update' : 'Add to Official Stock'}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
