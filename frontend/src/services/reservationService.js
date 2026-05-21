import { api, request } from './api';

export const createReservation = async (formData) => {
  try {
    const response = await fetch(api.createReservation, {
      method: 'POST',
      body: formData,
      credentials: 'include'
    });
    const data = await response.json();
    return {
      success: data.success || (data.status >= 200 && data.status < 300),
      ...data
    };
  } catch (error) {
    return { success: false, message: error.message };
  }
};

export const cancelReservation = async (reservationId) => {
  return await request(api.cancelReservation, {
    method: 'POST',
    body: JSON.stringify({ id: reservationId })
  });
};

export const getPharmacyReservations = async (token) => {
  return await request(api.getReservations, {
    headers: { 'Authorization': `Bearer ${token}` }
  });
};

