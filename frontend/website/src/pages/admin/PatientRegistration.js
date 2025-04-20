import React, { useState } from 'react';
import AdminSidebar from '../../components/AdminSidebar';
import { APP_URL } from '../../App';

const PatientRegistration = () => {
  const [formData, setFormData] = useState({
    FirstName: '',
    LastName: '',
    Email: '',
    Password: '',
    UserType: 'Patient',
    ContactNumber: '',
  });
  const [successMessage, setSuccessMessage] = useState('');
  const [errorMessage, setErrorMessage] = useState('');

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await fetch(`${APP_URL}/registerPatient.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (response.ok && data.status === 1) {
        setSuccessMessage('Patient registered successfully.');
        setErrorMessage('');
        setFormData({
          FirstName: '',
          LastName: '',
          Email: '',
          Password: '',
          UserType: 'Patient',
          ContactNumber: '',
        });
      } else {
        setErrorMessage(data.message || 'Failed to register patient.');
        setSuccessMessage('');
      }
    } catch (error) {
      console.error('Error registering patient:', error);
      setErrorMessage('Error: Failed to register patient.');
      setSuccessMessage('');
    }
  };

  return (
    <div className="container-scroller">
      <div className="container-fluid page-body-wrapper">
        <AdminSidebar />
        <div className="main-panel">
          <div className="content-wrapper">
            <div className="page-header">
              <h2 style={{ color: '#007bff' }}>Register Patient</h2>
            </div>
            {successMessage && (
              <div className="alert alert-success">{successMessage}</div>
            )}
            {errorMessage && (
              <div className="alert alert-danger">{errorMessage}</div>
            )}
            <form onSubmit={handleSubmit}>
              <div className="form-group">
                <label htmlFor="FirstName">First Name</label>
                <input
                  type="text"
                  className="form-control"
                  id="FirstName"
                  name="FirstName"
                  value={formData.FirstName}
                  onChange={handleInputChange}
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="LastName">Last Name</label>
                <input
                  type="text"
                  className="form-control"
                  id="LastName"
                  name="LastName"
                  value={formData.LastName}
                  onChange={handleInputChange}
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="Email">Email</label>
                <input
                  type="email"
                  className="form-control"
                  id="Email"
                  name="Email"
                  value={formData.Email}
                  onChange={handleInputChange}
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="Password">Password</label>
                <input
                  type="password"
                  className="form-control"
                  id="Password"
                  name="Password"
                  value={formData.Password}
                  onChange={handleInputChange}
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="ContactNumber">Contact Number</label>
                <input
                  type="text"
                  className="form-control"
                  id="ContactNumber"
                  name="ContactNumber"
                  value={formData.ContactNumber}
                  onChange={handleInputChange}
                  required
                />
              </div>
              <button type="submit" className="btn btn-primary">
                Register Patient
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PatientRegistration;