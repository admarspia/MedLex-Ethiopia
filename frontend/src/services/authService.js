import { api, request } from './api';

export const registerPharmacy = async (formData) => {
  try {
    const response = await fetch(api.register, {
      method: 'POST',
      body: formData,
      credentials: 'include'
    });
    const data = await response.json();
    
    if (data.success || (data.status >= 200 && data.status < 300)) {
      if (data.data?.token) {
        localStorage.setItem('token', data.data.token);
      }
      return { success: true, data: data.data };
    }
    return { success: false, message: data.data || data.message };
  } catch (error) {
    return { success: false, message: error.message };
  }
};

export const loginPharmacy = async (email, password) => {
  try {
    const response = await fetch(api.login, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password }),
      credentials: 'include'
    });
    const data = await response.json();
    
    if (data.success || (data.status >= 200 && data.status < 300)) {
      if (data.data?.token) {
        localStorage.setItem('token', data.data.token);
      }
      return { success: true, data: data.data };
    }
    return { success: false, message: data.data || data.message };
  } catch (error) {
    return { success: false, message: error.message };
  }
};

export const logout = () => {
  localStorage.removeItem('token');
  sessionStorage.clear();
};

export const getSession = async () => {
  return await request(api.getSession);
};
