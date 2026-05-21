import React, { createContext, useState, useContext, useEffect } from 'react';
import { loginPharmacy, registerPharmacy, logout, getSession } from '../services/authService';

const AuthContext = createContext();

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        const result = await getSession();
        if (result.success && result.data) {
          setUser({ token, id: result.data, email: localStorage.getItem('userEmail') });
        } else {
          logout();
        }
      } catch (error) {
        logout();
      }
    }
    setLoading(false);
  };

  const login = async (credentials) => {
    const result = await loginPharmacy(credentials.email, credentials.password);
    if (result.success && result.data?.token) {
      localStorage.setItem('token', result.data.token);
      localStorage.setItem('userEmail', credentials.email);
      setUser({ token: result.data.token, email: credentials.email });
      return { success: true };
    }
    return { success: false, message: result.message || 'Login failed' };
  };

  const register = async (formData) => {
    const result = await registerPharmacy(formData);
    if (result.success && result.data?.token) {
      localStorage.setItem('token', result.data.token);
      localStorage.setItem('userEmail', formData.get('email'));
      setUser({ token: result.data.token, email: formData.get('email') });
      return { success: true };
    }
    return { success: false, message: result.message || 'Registration failed' };
  };

  const logoutUser = () => {
    logout();
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, register, logout: logoutUser, loading }}>
      {children}
    </AuthContext.Provider>
  );
};
