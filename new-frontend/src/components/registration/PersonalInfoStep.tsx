import React from 'react';
import { UseFormRegister, FieldErrors } from 'react-hook-form';
import { ApplicantRegistrationData } from '@/lib/registration';

interface PersonalInfoStepProps {
  register: UseFormRegister<ApplicantRegistrationData>;
  errors: FieldErrors<ApplicantRegistrationData>;
}

export default function PersonalInfoStep({ register, errors }: PersonalInfoStepProps) {
  return (
    <div className="space-y-6">
      <h3 className="text-xl font-semibold text-gray-900 mb-6">Data Pribadi</h3>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Nama Depan *
          </label>
          <input
            {...register('first_name', { required: 'Nama depan wajib diisi' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Masukkan nama depan Anda"
          />
          {errors.first_name && (
            <p className="mt-1 text-sm text-red-600">{errors.first_name.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Nama Belakang *
          </label>
          <input
            {...register('last_name', { required: 'Nama belakang wajib diisi' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Masukkan nama belakang Anda"
          />
          {errors.last_name && (
            <p className="mt-1 text-sm text-red-600">{errors.last_name.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Email *
          </label>
          <input
            {...register('email', { 
              required: 'Email wajib diisi',
              pattern: {
                value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                message: 'Format email tidak valid'
              }
            })}
            type="email"
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="contoh@email.com"
          />
          {errors.email && (
            <p className="mt-1 text-sm text-red-600">{errors.email.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Nomor Telepon *
          </label>
          <input
            {...register('phone', { required: 'Nomor telepon wajib diisi' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="+6281234567890"
          />
          {errors.phone && (
            <p className="mt-1 text-sm text-red-600">{errors.phone.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            NIK (16 digit) *
          </label>
          <input
            {...register('nik', { 
              required: 'NIK wajib diisi',
              pattern: {
                value: /^\d{16}$/,
                message: 'NIK harus 16 digit angka'
              }
            })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="1234567890123456"
            maxLength={16}
          />
          {errors.nik && (
            <p className="mt-1 text-sm text-red-600">{errors.nik.message}</p>
          )}
          <p className="mt-1 text-xs text-gray-500">NIK akan digunakan sebagai password</p>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Tanggal Lahir *
          </label>
          <input
            {...register('birth_date', { required: 'Tanggal lahir wajib diisi' })}
            type="date"
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
          {errors.birth_date && (
            <p className="mt-1 text-sm text-red-600">{errors.birth_date.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Tempat Lahir *
          </label>
          <input
            {...register('birth_place', { required: 'Tempat lahir wajib diisi' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Jakarta"
          />
          {errors.birth_place && (
            <p className="mt-1 text-sm text-red-600">{errors.birth_place.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Jenis Kelamin *
          </label>
          <select
            {...register('gender', { required: 'Jenis kelamin wajib dipilih' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="male">Laki-laki</option>
            <option value="female">Perempuan</option>
          </select>
          {errors.gender && (
            <p className="mt-1 text-sm text-red-600">{errors.gender.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Agama
          </label>
          <input
            {...register('religion')}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Islam"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Status Pernikahan *
          </label>
          <select
            {...register('marital_status', { required: 'Status pernikahan wajib dipilih' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="single">Belum Menikah</option>
            <option value="married">Menikah</option>
            <option value="divorced">Cerai</option>
            <option value="widowed">Janda/Duda</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Tinggi Badan (cm)
          </label>
          <input
            {...register('height', { 
              min: { value: 100, message: 'Tinggi minimal 100 cm' },
              max: { value: 250, message: 'Tinggi maksimal 250 cm' }
            })}
            type="number"
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="175"
          />
          {errors.height && (
            <p className="mt-1 text-sm text-red-600">{errors.height.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Berat Badan (kg)
          </label>
          <input
            {...register('weight', { 
              min: { value: 30, message: 'Berat minimal 30 kg' },
              max: { value: 200, message: 'Berat maksimal 200 kg' }
            })}
            type="number"
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="70"
          />
          {errors.weight && (
            <p className="mt-1 text-sm text-red-600">{errors.weight.message}</p>
          )}
        </div>
      </div>
    </div>
  );
}
