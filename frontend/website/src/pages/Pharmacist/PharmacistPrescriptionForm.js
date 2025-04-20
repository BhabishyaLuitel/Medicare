import React, { useState, useEffect } from "react";
import PharmacistSidebar from "../../components/PharmacistSidebar";
import { APP_URL } from "../../App";

function PrescriptionForm() {
  const [patients, setPatients] = useState([]);
  const [pharmacists, setPharmacists] = useState([]);
  const [selectedPatient, setSelectedPatient] = useState("");
  const [selectedPharmacist, setSelectedPharmacist] = useState("");
  const [medicineList, setMedicineList] = useState([]);
  const [prescriptionName, setPrescriptionName] = useState("");
  const [prescribedBy, setPrescribedBy] = useState("");
  const [successMessage, setSuccessMessage] = useState("");
  const [errorMessage, setErrorMessage] = useState("");

  useEffect(() => {
    fetchPatients();
    fetchPharmacists();
  }, []);

  const fetchPatients = async () => {
    try {
      const response = await fetch(`${APP_URL}/getpatients.php`);
      const data = await response.json();
      setPatients(data);
    } catch (error) {
      console.error("Error fetching patients:", error);
    }
  };

  const fetchPharmacists = async () => {
    try {
      const response = await fetch(`${APP_URL}/getpharmacists.php`);
      const data = await response.json();
      setPharmacists(data);
    } catch (error) {
      console.error("Error fetching pharmacists:", error);
    }
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    if (!selectedPatient) {
      setErrorMessage("Error: Patient ID is required.");
      return;
    }

    const userId = parseInt(selectedPatient, 10);

    const sanitizedMedicines = (medicineList || []).map((medicine) =>
      medicine.trim().replace(/\r?\n|\r/g, "")
    );

    const selectedPharmacistObj = pharmacists.find(
      (pharmacist) => pharmacist.ID === selectedPharmacist
    );
    const pharmacistFullName = selectedPharmacistObj
      ? `${selectedPharmacistObj.FirstName} ${selectedPharmacistObj.LastName}`
      : "";

    const data = {
      user_id: userId,
      pharmacist: pharmacistFullName,
      prescribedBy: prescribedBy,
      medicines: sanitizedMedicines,
      name: prescriptionName,
    };

    try {
      const response = await fetch(`${APP_URL}/createPrescription.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });
      const responseData = await response.json();
      if (response.ok) {
        setSuccessMessage("Prescription added successfully.");
        setErrorMessage("");
        setSelectedPatient("");
        setSelectedPharmacist("");
        setMedicineList([]);
        setPrescriptionName("");
        setPrescribedBy("");
      } else {
        setErrorMessage(responseData);
        setSuccessMessage("");
      }
    } catch (error) {
      console.error("Error adding prescription:", error);
      setErrorMessage("Error: Failed to add prescription.");
      setSuccessMessage("");
    }
  };

  const addMedicine = () => {
    setMedicineList([...medicineList, ""]);
  };

  const handleMedicineChange = (index, value) => {
    const updatedMedicines = [...medicineList];
    updatedMedicines[index] = value;
    setMedicineList(updatedMedicines);
  };

  return (
    <>
      <PharmacistSidebar />
      <div style={{ maxWidth: "800px", margin: "0 auto", padding: "20px" }}>
        <div style={{ maxWidth: "600px", margin: "0 auto", padding: "20px" }}>
          <h2 style={{ color: "#007bff", marginBottom: "20px" }}>
            Add Prescription
          </h2>
          {successMessage && (
            <div style={{ color: "green", marginBottom: "10px" }}>
              {successMessage}
            </div>
          )}
          {errorMessage && (
            <div style={{ color: "red", marginBottom: "10px" }}>
              {errorMessage}
            </div>
          )}
          <form onSubmit={handleSubmit}>
            <div style={{ marginBottom: "20px" }}>
              <label htmlFor="patient">Select Patient:</label>
              <select
                id="patient"
                value={selectedPatient}
                onChange={(e) => setSelectedPatient(e.target.value)}
                style={{ marginLeft: "10px" }}
              >
                <option value="">Select</option>
                {patients.map((patient) => (
                  <option key={patient.ID} value={patient.ID}>
                    {patient.FirstName} {patient.LastName}
                  </option>
                ))}
              </select>
            </div>
            <div style={{ marginBottom: "20px" }}>
              <label htmlFor="pharmacist">Select Pharmacist:</label>
              <select
                id="pharmacist"
                value={selectedPharmacist}
                onChange={(e) => setSelectedPharmacist(e.target.value)}
                style={{ marginLeft: "10px" }}
              >
                <option value="">Select</option>
                {pharmacists.map((pharmacist) => (
                  <option key={pharmacist.ID} value={pharmacist.ID}>
                    {pharmacist.FirstName} {pharmacist.LastName}
                  </option>
                ))}
              </select>
            </div>
            <div style={{ marginBottom: "20px" }}>
              <label htmlFor="prescribedBy">Prescribed By:</label>
              <input
                type="text"
                id="prescribedBy"
                value={prescribedBy}
                onChange={(e) => setPrescribedBy(e.target.value)}
                style={{ marginLeft: "10px" }}
              />
            </div>
            <div style={{ marginBottom: "20px" }}>
              <label>Enter Medicines:</label>
              {medicineList.map((medicine, index) => (
                <div key={index} style={{ marginBottom: "10px" }}>
                  <input
                    type="text"
                    value={medicine}
                    onChange={(e) =>
                      handleMedicineChange(index, e.target.value)
                    }
                    style={{ marginRight: "10px" }}
                  />
                </div>
              ))}
              <button
                type="button"
                onClick={addMedicine}
                style={{
                  backgroundColor: "#007bff",
                  color: "#fff",
                  border: "none",
                  padding: "5px 10px",
                  cursor: "pointer",
                }}
              >
                Add Medicine
              </button>
            </div>
            <div style={{ marginBottom: "20px" }}>
              <label htmlFor="prescriptionName">Prescription Name:</label>
              <input
                type="text"
                id="prescriptionName"
                value={prescriptionName}
                onChange={(e) => setPrescriptionName(e.target.value)}
                style={{ marginLeft: "10px" }}
              />
            </div>
            <button
              type="submit"
              style={{
                backgroundColor: "#007bff",
                color: "#fff",
                border: "none",
                padding: "10px 20px",
                cursor: "pointer",
              }}
            >
              Add Prescription
            </button>
          </form>
        </div>
      </div>
    </>
  );
}

export default PrescriptionForm;
