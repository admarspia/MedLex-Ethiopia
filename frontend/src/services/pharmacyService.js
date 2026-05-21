import { api, request } from './api';

export const getPharmacies = async () => {
  return await request(api.getPharmacies);
};

export const getPharmacyInventory = async (token) => {
  return await request(api.getMedicines, {
    headers: { 'Authorization': `Bearer ${token}` }
  });
};
