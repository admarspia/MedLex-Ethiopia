const API_URL = "http://localhost/backend/api";

export const getPharmacies = async () => {
    try {
        const response = await fetch(`${API_URL}/Pharmacy.php`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching pharmacies:', error);
        return [];
    }
};
