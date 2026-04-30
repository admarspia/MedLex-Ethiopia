import { createContext, useState, useContext, useEffect } from 'react';

const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const API_AUTH_URL = "http://localhost:8000";

  useEffect(() => {
    const token = localStorage.getItem('medlex_token');
    const email = localStorage.getItem('medlex_email');
    if (token && email) {
      setUser({ email, token });
    }
  }, []);

  const login = async (userData) => {
    try {
      console.log("received :" , userData);
      const response = await fetch(`${API_AUTH_URL}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: "include",
        body: JSON.stringify({
          email: userData.email,
          password: userData.password
        })
      });
      const data = await response.json();

      if (data.status == 200) {
        localStorage.setItem('medlex_token', data.data.token);
        localStorage.setItem('medlex_email', userData.email);
        setUser({ email: userData.email, token: data.data.token });
        return { success: true };
      }
      return { success: false, message: data.message || "Login failed." };
    } catch (e) {
      return { success: false, message: "Network error. Make sure the PHP server is running on localhost:8000" };
    }
  };

  const register = async (userData) => {
    try {
      const formData = new FormData();
      formData.append('name', userData.name);
      formData.append('address', userData.address || '');
      formData.append('phone', userData.phone || '');
      formData.append('email', userData.email);
      formData.append('password', userData.password);

      if (userData.licenseFile) {
        formData.append('license', userData.licenseFile);
      }

      const response = await fetch(`${API_AUTH_URL}/register`, {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        localStorage.setItem('medlex_token', data.data.token);
        localStorage.setItem('medlex_email', userData.email);
        setUser({ email: userData.email, token: data.data.token });
        return { success: true };
      }
      return { success: false, message: data.message || "Registration failed." };
    } catch (e) {
      return { success: false, message: "Network error. Make sure the PHP server is running on localhost:8000" };
    }
  };

  const logout = () => {
    localStorage.removeItem('medlex_token');
    localStorage.removeItem('medlex_email');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);
