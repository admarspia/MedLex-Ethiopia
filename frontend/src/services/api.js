const API_BASE_URL = "http://localhost:8000";

export const api = {
  // Auth endpoints
  register: `${API_BASE_URL}/register`,
  login: `${API_BASE_URL}/login`,
  getSession: `${API_BASE_URL}/get-session`,
  
  // Pharmacy endpoints
  addMedicine: `${API_BASE_URL}/add-medicine`,
  removeMedicine: `${API_BASE_URL}/remove-medicine`,
  getMedicines: `${API_BASE_URL}/get-medicines`,
  getPharmacies: `${API_BASE_URL}/get-pharmacies`,
  updatePrice: `${API_BASE_URL}/update-price`,
  
  // Medicine endpoints
  searchMedicine: `${API_BASE_URL}/search-medicine`,
  getMedicineById: (id) => `${API_BASE_URL}/medicine?id=${id}`,
  
  // Reservation endpoints
  createReservation: `${API_BASE_URL}/reservation/create`,
  cancelReservation: `${API_BASE_URL}/reservation/cancel`,
  getReservations: `${API_BASE_URL}/reservation/list`,
  notifyExpiring: `${API_BASE_URL}/reservation/notify-expiring`
};

export const request = async (url, options = {}) => {
  const token = localStorage.getItem('token');
  
  const headers = {
    'Accept': 'application/json',
    ...options.headers
  };
  
  // Don't set Content-Type for FormData (browser will set it with boundary)
  if (!(options.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
  }
  
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  const config = {
    ...options,
    headers,
    credentials: 'include',
    mode: 'cors' // Explicitly set CORS mode
  };
  
  // Convert body to JSON if it's not FormData
  if (config.body && !(config.body instanceof FormData) && typeof config.body === 'object') {
    config.body = JSON.stringify(config.body);
  }
  
  try {
    const response = await fetch(url, config);
    
    // Check if response is OK
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`HTTP ${response.status}: ${errorText}`);
    }
    
    const data = await response.json();
    
    return {
      success: data.success || (data.status >= 200 && data.status < 300),
      ...data
    };
  } catch (error) {
    console.error('API Error:', error);
    return { 
      success: false, 
      message: error.message,
      data: null 
    };
  }
};
