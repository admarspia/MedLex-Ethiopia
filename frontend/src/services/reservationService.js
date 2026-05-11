const API_URL = "http://localhost/backend/api";

export const getReservations = async () => {
    try {
        const response = await fetch(`${API_URL}/Reservation.php`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching reservations:', error);
        return [];
    }
};

export const createReservation = async (data) => {
    try {
        const response = await fetch(`${API_URL}/Reservation.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
        return await response.json();
    } catch (error) {
        console.error('Error creating reservation:', error);
        return { success: false };
    }
};
