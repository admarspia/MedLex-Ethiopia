const API_URL = "http://localhost:8000";

export const getPharmacies = async () => {
    try {
        const response = await fetch(`${API_URL}/pharmacies`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching pharmacies:', error);
        return { success: false, data: [] };
    }
};

export const registerPharmacy = async (formData) => {
    try {
        const response = await fetch(`${API_URL}/register`, {
            method: 'POST',
            body: formData
        });
        return await response.json();
    } catch (error) {
        console.error('Error registering pharmacy:', error);
        return { success: false, message: error.message };
    }
};

export const loginPharmacy = async (email, password) => {
    try {
        const response = await fetch(`${API_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        return await response.json();
    } catch (error) {
        console.error('Error logging in:', error);
        return { success: false, message: error.message };
    }
};

export const getPharmacyInventory = async (token) => {
    try {
        const response = await fetch(`${API_URL}/pharmacy-inventory`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        return await response.json();
    } catch (error) {
        console.error('Error fetching inventory:', error);
        return { success: false, data: [] };
    }
};
