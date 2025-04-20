import React, { useState, useEffect } from 'react';
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Typography,
  Button,
} from '@mui/material';
import PharmacistSidebar from '../../components/PharmacistSidebar';
import { APP_URL } from '../../App';

const PersonalRecordsPage = () => {
  const [records, setRecords] = useState([]);
  const [alertMessage, setAlertMessage] = useState('');

  // Fetch personal records on component mount
  useEffect(() => {
    fetchPersonalRecords();
  }, []);

  // Fetch all patients and their personal records
  const fetchPersonalRecords = async () => {
    try {
      const response = await fetch(`${APP_URL}/getTblrecords.php`);
      if (!response.ok) {
        throw new Error('Failed to fetch personal records');
      }
      const data = await response.json();
      setRecords(data);
    } catch (error) {
      console.error('Error fetching personal records:', error);
    }
  };

  // Handle invoice generation for a specific patient
  const handleGenerateInvoice = async (patientId) => {
    try {
      const response = await fetch(`${APP_URL}/pharmacistInvoiceReport.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ patientId }),
      });

      const data = await response.json();
      if (response.ok) {
        setAlertMessage('Invoice generated successfully.');

        // Download the PDF
        const link = document.createElement('a');
        link.href = `${APP_URL}/${data.file}`; // Append the file name to APP_URL
        link.download = `invoice_${patientId}.pdf`;
        link.click();
      } else {
        setAlertMessage(data.error || 'Failed to generate invoice.');
      }
    } catch (error) {
      console.error('Error generating invoice:', error);
      setAlertMessage('Error: Failed to generate invoice.');
    }
  };

  return (
    <>
      <PharmacistSidebar />
      <div style={{ maxWidth: '1000px', margin: '0 auto', padding: '20px' }}>
        <Typography
          variant="h4"
          gutterBottom
          style={{ color: '#007bff', fontWeight: 'bold', marginBottom: '20px' }}
        >
          <strong>Patient Records</strong>
        </Typography>
        {alertMessage && (
          <div style={{ color: 'green', marginBottom: '10px' }}>
            {alertMessage}
          </div>
        )}
        <TableContainer
          component={Paper}
          style={{ backgroundColor: '#f5f5f5' }}
        >
          <Table>
            <TableHead>
              <TableRow>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  User ID
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  First Name
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Last Name
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  BP
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Diabetes
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Heart Health Issues
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Arthritis
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Allergies
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Other Issues
                </TableCell>
                <TableCell
                  style={{
                    color: '#007bff',
                    fontWeight: 'bold',
                    marginBottom: '20px',
                  }}
                >
                  Action
                </TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {records.map((record) => (
                <TableRow key={record.UserID}>
                  <TableCell>{record.UserID}</TableCell>
                  <TableCell>{record.FirstName || 'N/A'}</TableCell>
                  <TableCell>{record.LastName || 'N/A'}</TableCell>
                  <TableCell>{record.BP || 'N/A'}</TableCell>
                  <TableCell>{record.Diabetes || 'N/A'}</TableCell>
                  <TableCell>{record.HeartHealthIssues || 'N/A'}</TableCell>
                  <TableCell>{record.Arthritis || 'N/A'}</TableCell>
                  <TableCell>{record.Allergies || 'N/A'}</TableCell>
                  <TableCell>{record.OtherIssues || 'N/A'}</TableCell>
                  <TableCell>
                    <Button
                      variant="contained"
                      color="primary"
                      onClick={() => handleGenerateInvoice(record.UserID)}
                    >
                      Generate Invoice
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      </div>
    </>
  );
};

export default PersonalRecordsPage;
