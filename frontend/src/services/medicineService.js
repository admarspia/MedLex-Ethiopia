import { api, request } from './api';

export const searchMedicines = async (name) => {
  const result = await request(`${api.searchMedicine}?name=${encodeURIComponent(name)}`);
  return result;
};

export const getMedicineById = async (id) => {
  const result = await request(api.getMedicineById(id));
  return result;
};

export const addMedicineToStock = async (formData, token) => {
  try {
    const response = await fetch(api.addMedicine, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
      },
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

export const removeMedicineFromStock = async (medicineId, token) => {
  return await request(api.removeMedicine, {
    method: 'POST',
    body: JSON.stringify({ medicine_id: medicineId })
  });
};

export const updateMedicinePrice = async (medicineId, price, token) => {
  return await request(api.updatePrice, {
    method: 'POST',
    body: JSON.stringify({ medicine_id: medicineId, price })
  });
};

export const getPharmacyMedicines = async (pharmacyId) => {
  const url = pharmacyId ? `${api.getMedicines}?id=${pharmacyId}` : api.getMedicines;
  return await request(url);
};
