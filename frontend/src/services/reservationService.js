import { api, request } from './api';

export const createReservation = async (reservationData) => {
  try {
    // Handle FormData or JSON
    let options = {
      method: 'POST',
      credentials: 'include'
    };
    
    if (reservationData instanceof FormData) {
      options.body = reservationData;
    } else {
      options.headers = { 'Content-Type': 'application/json' };
      options.body = JSON.stringify(reservationData);
    }
    
    const response = await fetch(api.createReservation, options);
    const data = await response.json();
    
    return {
      success: data.success || (data.status >= 200 && data.status < 300),
      ...data
    };
  } catch (error) {
    console.error('Create reservation error:', error);
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
    headers: token ? { 'Authorization': `Bearer ${token}` } : {}
  });
};

export const getUserReservations = async (email) => {
  return await request(api.getReservations + `?email=${encodeURIComponent(email)}`);
};

export const checkReservationStatus = (reservation) => {
  const now = new Date();
  const expiry = new Date(reservation.expiration_date);
  
  if (expiry < now) {
    return 'expired';
  }
  
  const hoursLeft = (expiry - now) / (1000 * 60 * 60);
  if (hoursLeft < 1) {
    return 'expiring_soon';
  }
  
  return 'active';
};
