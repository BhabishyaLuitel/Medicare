import React, { useState, useEffect } from 'react';
import { FaCheck, FaTimes } from 'react-icons/fa';
import AdminSidebar from '../../components/AdminSidebar';
import { APP_URL } from '../../App';

const ManagePatientAdmin = () => {
  const [patients, setPatients] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchPatients();
  }, []);

  const fetchPatients = async () => {
    try {
      const response = await fetch(
        `${APP_URL}/getpatients.php`,
        {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        }
      );

      if (response.ok) {
        const data = await response.json();
        setPatients(data);
        setError(null); // Clear any previous errors
      } else {
        setError('Error fetching patients. Please try again later.');
      }
    } catch (error) {
      console.error('Error fetching patients:', error);
      setError('Error fetching patients. Please try again later.');
    }
  };

  const handleToggleStatus = async (ID) => {
    try {
      const response = await fetch(
        `${APP_URL}/getpatients.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ ID: ID }),
        }
      );

      if (response.ok) {
        fetchPatients();
        console.log('Account status toggled successfully');
        setError(null); // Clear any previous errors
      } else {
        setError('Error toggling account status. Please try again later.');
      }
    } catch (error) {
      console.error('Error:', error);
      setError('Error toggling account status. Please try again later.');
    }
  };

  return (
    <div className="container-scroller">
      <div className="container-fluid page-body-wrapper">
        <AdminSidebar />
        <div className="main-panel">
          <div className="content-wrapper">
            <div className="page-header">
              <h2 style={{ color: '#007bff' }}>Manage Patients</h2>
            </div>
            {error && <div className="alert alert-danger">{error}</div>}
            <div className="table-responsive">
              <table className="table">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Patient User Id</th>
                    <th>Patient Name</th>
                  </tr>
                </thead>
                <tbody>
                  {patients.map((patient, index) => (
                    <tr key={patient.ID}>
                      <td>{index + 1}</td>
                      <td>{patient.ID}</td>
                      <td>
                        {patient.FirstName} {patient.LastName}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ManagePatientAdmin;
