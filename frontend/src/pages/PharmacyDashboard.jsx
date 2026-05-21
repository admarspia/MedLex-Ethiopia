import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { getPharmacyInventory } from '../services/pharmacyService';
import { addMedicineToStock, removeMedicineFromStock, updateMedicinePrice } from '../services/medicineService';
import { Store, LogOut, Plus, Trash2, Edit2, X, AlertCircle, Package } from 'lucide-react';

function PharmacyDashboard() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [inventory, setInventory] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const [selectedItem, setSelectedItem] = useState(null);
  const [formData, setFormData] = useState({
    generic_name: '',
    count: '',
    price: ''
  });
  const [imageFile, setImageFile] = useState(null);
  const [message, setMessage] = useState({ type: '', text: '' });

  useEffect(() => {
    if (!user) {
      navigate('/login');
      return;
    }
    fetchInventory();
  }, [user, navigate]);

  const fetchInventory = async () => {
    setLoading(true);
    const result = await getPharmacyInventory(user.token);
    if (result.success && result.data) {
      setInventory(Array.isArray(result.data) ? result.data : []);
    }
    setLoading(false);
  };

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  const openAddModal = () => {
    setEditMode(false);
    setSelectedItem(null);
    setFormData({ generic_name: '', count: '', price: '' });
    setImageFile(null);
    setIsModalOpen(true);
  };

  const openEditModal = (item) => {
    setEditMode(true);
    setSelectedItem(item);
    setFormData({
      generic_name: item.generic_name,
      count: item.count,
      price: item.price
    });
    setImageFile(null);
    setIsModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage({ type: '', text: '' });
    
    let result;
    if (editMode) {
      result = await updateMedicinePrice(selectedItem.id, formData.price, user.token);
    } else {
      const submitData = new FormData();
      submitData.append('generic_name', formData.generic_name);
      submitData.append('count', formData.count);
      submitData.append('price', formData.price);
      if (imageFile) {
        submitData.append('image', imageFile);
      }
      result = await addMedicineToStock(submitData, user.token);
    }

    if (result.success) {
      setMessage({ type: 'success', text: editMode ? 'Price updated successfully!' : 'Medicine added to inventory!' });
      setIsModalOpen(false);
      fetchInventory();
      setTimeout(() => setMessage({ type: '', text: '' }), 3000);
    } else {
      setMessage({ type: 'error', text: result.message || 'Operation failed' });
    }
  };

  const handleRemove = async (item) => {
    if (!confirm(`Remove ${item.generic_name} from inventory?`)) return;
    
    const result = await removeMedicineFromStock(item.id, user.token);
    if (result.success) {
      setMessage({ type: 'success', text: 'Medicine removed from inventory' });
      fetchInventory();
      setTimeout(() => setMessage({ type: '', text: '' }), 3000);
    } else {
      setMessage({ type: 'error', text: result.message || 'Failed to remove medicine' });
    }
  };

  if (!user) return null;

  return (
    <div className="container" style={{ padding: '4rem 0' }}>
      {/* Header */}
      <div className="card" style={{ padding: '2rem', marginBottom: '2rem', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '1rem' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
          <div style={{ width: '56px', height: '56px', borderRadius: '14px', background: '#000', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff' }}>
            <Store size={28} />
          </div>
          <div>
            <h1 style={{ fontSize: '1.5rem', marginBottom: '0.25rem' }}>Pharmacy Dashboard</h1>
            <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>{user.email || 'Authorized Pharmacy'}</p>
          </div>
        </div>
        <button onClick={handleLogout} className="btn btn-outline" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
          <LogOut size={16} /> Logout
        </button>
      </div>

      {/* Message */}
      {message.text && (
        <div style={{ 
          padding: '1rem', 
          marginBottom: '1rem', 
          borderRadius: '8px',
          backgroundColor: message.type === 'success' ? '#d4edda' : '#f8d7da',
          color: message.type === 'success' ? '#155724' : '#721c24',
          border: `1px solid ${message.type === 'success' ? '#c3e6cb' : '#f5c6cb'}`
        }}>
          {message.text}
        </div>
      )}

      {/* Header Actions */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem', flexWrap: 'wrap', gap: '1rem' }}>
        <div>
          <h2 style={{ fontSize: '1.8rem', marginBottom: '0.25rem' }}>Inventory Stock</h2>
          <p style={{ color: 'var(--text-muted)' }}>Manage your medication availability and pricing</p>
        </div>
        <button onClick={openAddModal} className="btn btn-primary" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
          <Plus size={18} /> Add New Medicine
        </button>
      </div>

      {/* Inventory Table */}
      <div className="card" style={{ padding: '0', overflow: 'hidden' }}>
        {loading ? (
          <div style={{ padding: '4rem', textAlign: 'center' }}>Loading inventory...</div>
        ) : inventory.length === 0 ? (
          <div style={{ padding: '4rem', textAlign: 'center' }}>
            <Package size={48} style={{ opacity: 0.3, marginBottom: '1rem' }} />
            <p>No medicines in inventory. Click "Add New Medicine" to get started.</p>
          </div>
        ) : (
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ background: '#f5f5f5' }}>
                <th style={{ padding: '1rem', textAlign: 'left' }}>Medicine Name</th>
                <th style={{ padding: '1rem', textAlign: 'left' }}>Brand</th>
                <th style={{ padding: '1rem', textAlign: 'center' }}>Stock</th>
                <th style={{ padding: '1rem', textAlign: 'right' }}>Price (ETB)</th>
                <th style={{ padding: '1rem', textAlign: 'center' }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {inventory.map((item, index) => (
                <tr key={index} style={{ borderTop: '1px solid #eee' }}>
                  <td style={{ padding: '1rem', fontWeight: 600 }}>{item.generic_name}</td>
                  <td style={{ padding: '1rem', color: 'var(--text-muted)' }}>{item.brand_name || '-'}</td>
                  <td style={{ padding: '1rem', textAlign: 'center' }}>
                    <span className={`badge ${item.count > 10 ? 'badge-red' : 'badge-outline'}`}>
                      {item.count} units
                    </span>
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'right', fontWeight: 800, color: 'var(--color-primary)' }}>
                    {item.price} ETB
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'center' }}>
                    <div style={{ display: 'flex', gap: '0.5rem', justifyContent: 'center' }}>
                      <button 
                        onClick={() => openEditModal(item)} 
                        style={{ background: 'transparent', border: 'none', cursor: 'pointer', padding: '0.25rem' }}
                        title="Edit Price"
                      >
                        <Edit2 size={18} color="#666" />
                      </button>
                      <button 
                        onClick={() => handleRemove(item)} 
                        style={{ background: 'transparent', border: 'none', cursor: 'pointer', padding: '0.25rem' }}
                        title="Remove from Inventory"
                      >
                        <Trash2 size={18} color="var(--color-primary)" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {/* Modal */}
      {isModalOpen && (
        <div style={{ 
          position: 'fixed', 
          inset: 0, 
          background: 'rgba(0,0,0,0.5)', 
          backdropFilter: 'blur(4px)', 
          display: 'flex', 
          alignItems: 'center', 
          justifyContent: 'center', 
          zIndex: 1000,
          padding: '1rem'
        }}>
          <div className="card" style={{ width: '100%', maxWidth: '500px', maxHeight: '90vh', overflow: 'auto' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.5rem' }}>
              <h2 style={{ fontSize: '1.5rem' }}>{editMode ? 'Update Price' : 'Add Medicine to Inventory'}</h2>
              <button onClick={() => setIsModalOpen(false)} style={{ background: 'transparent', border: 'none', cursor: 'pointer' }}>
                <X size={24} />
              </button>
            </div>
            
            <form onSubmit={handleSubmit}>
              <div style={{ marginBottom: '1rem' }}>
                <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>Generic Name</label>
                <input
                  type="text"
                  className="search-input"
                  value={formData.generic_name}
                  onChange={(e) => setFormData({ ...formData, generic_name: e.target.value })}
                  required
                  disabled={editMode}
                  style={{ width: '100%' }}
                />
                {editMode && <p style={{ fontSize: '0.75rem', color: '#999', marginTop: '0.25rem' }}>Generic name cannot be changed</p>}
              </div>
              
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1rem' }}>
                <div>
                  <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>Quantity</label>
                  <input
                    type="number"
                    className="search-input"
                    value={formData.count}
                    onChange={(e) => setFormData({ ...formData, count: e.target.value })}
                    required={!editMode}
                    disabled={editMode}
                    style={{ width: '100%' }}
                  />
                </div>
                <div>
                  <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>Price (ETB)</label>
                  <input
                    type="number"
                    step="0.01"
                    className="search-input"
                    value={formData.price}
                    onChange={(e) => setFormData({ ...formData, price: e.target.value })}
                    required
                    style={{ width: '100%' }}
                  />
                </div>
              </div>
              
              {!editMode && (
                <div style={{ marginBottom: '1.5rem' }}>
                  <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600 }}>Medicine Image (Optional)</label>
                  <input
                    type="file"
                    accept="image/jpeg,image/png"
                    onChange={(e) => setImageFile(e.target.files[0])}
                    style={{ width: '100%' }}
                  />
                </div>
              )}
              
              <div style={{ background: 'var(--color-primary-light)', padding: '1rem', borderRadius: '8px', marginBottom: '1.5rem', display: 'flex', gap: '0.5rem', alignItems: 'flex-start' }}>
                <AlertCircle size={20} color="var(--color-primary)" />
                <p style={{ fontSize: '0.85rem', color: 'var(--color-primary)', margin: 0 }}>
                  {editMode ? "Price updates will reflect immediately in search results." : "This medication will be visible to all users in the discovery network."}
                </p>
              </div>
              
              <button type="submit" className="btn btn-primary" style={{ width: '100%', padding: '1rem' }}>
                {editMode ? 'Update Price' : 'Add to Inventory'}
              </button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

export default PharmacyDashboard;
