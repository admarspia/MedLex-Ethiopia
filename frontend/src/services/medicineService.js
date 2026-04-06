const API_URL = "http://localhost/backend/api";

export const getMedicines = async (search = '') => {
    try {
        const url = `${API_URL}/Medicine.php${search ? `?search=${search}` : ''}`;
        const response = await fetch(url);
        return await response.json();
    } catch (error) {
        console.error('Error fetching medicines:', error);
        return [];
    }
};

export const getMedicineById = async (id) => {
    try {
        const response = await fetch(`${API_URL}/Medicine.php?id=${id}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching medicine details:', error);
        return null;
    }
};
