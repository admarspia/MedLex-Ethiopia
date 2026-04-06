import { createContext, useState, useContext, useEffect } from 'react';

const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const API_AUTH_URL = "http://localhost/backend/index.php";

  useEffect(() => {
    const token = localStorage.getItem('medlex_token');
    const email = localStorage.getItem('medlex_email');
    if (token && email) {
      setUser({ email, token });
    }
  }, []);

  const login = async (userData) => {
    try {
      const formData = new FormData();
      formData.append('email', userData.email);
      formData.append('password', userData.password);

      const response = await fetch(`${API_AUTH_URL}/login`, {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.status === 200) {
        localStorage.setItem('medlex_token', data.data.token);
        localStorage.setItem('medlex_email', userData.email);
        setUser({ email: userData.email, token: data.data.token });
        return { success: true };
      }
      return { success: false, message: data.data || "Login failed. Please check your credentials." };
    } catch (e) {
      return { success: false, message: "Network error. Backend might not be running on expected path." };
    }
  };

  const register = async (userData) => {
    try {
      const formData = new FormData();
      formData.append('name', userData.name);
      formData.append('address', userData.address);
      formData.append('phone', userData.phone);
      formData.append('email', userData.email);
      formData.append('password', userData.password);

      if (userData.licenseFile) {
        formData.append('license', userData.licenseFile);
      } else {
        const fileBlob = new Blob(['dummy license'], { type: 'text/plain' });
        formData.append('license', fileBlob, 'license.txt');
      }

      const response = await fetch(`${API_AUTH_URL}/register`, {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.status === 201) {
        localStorage.setItem('medlex_token', data.data.token);
        localStorage.setItem('medlex_email', userData.email);
        setUser({ email: userData.email, token: data.data.token });
        return { success: true };
      }
      return { success: false, message: data.data || "Registration failed. Validation error." };
    } catch (e) {
      return { success: false, message: "Network error. Backend might not be running on expected path." };
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
