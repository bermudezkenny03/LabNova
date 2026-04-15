# Testing Guide: Role-Based Access Control (RBAC) System

## Application Status

✅ **Backend**: Running on http://localhost:8000 (Laravel with permission middleware)
✅ **Frontend**: Running on http://localhost:3001 (React with protected routes & components)
✅ **Database**: SQLite at `backend-lab-nova/database/database.sqlite`

---

## Test Users

All test users have password: **`Password123!`**

### 1. **Estudiante (Student)**
- **Email**: estudiante@test.com
- **Expected Access**:
  - ✅ Login successful
  - ✅ Dashboard (limited view - only quick access links)
  - ✅ Reservations: Can create & view only **own** reservations
  - ✅ Equipment: Can view list (read-only)
  - ❌ Cannot access Users page
  - ❌ Cannot access Reports page
  - ❌ Cannot approve/reject reservations
  - ❌ Cannot edit/delete equipment
- **Test Actions**:
  1. Login → Should see limited dashboard
  2. Go to Reservations → Should only see own reservations
  3. Try accessing `/users` → Should be redirected
  4. Approval buttons should be greyed out/hidden

### 2. **Docente (Teacher)**
- **Email**: docente@test.com
- **Expected Access**:
  - ✅ Full Dashboard with equipment & reservations stats
  - ✅ Reservations: Create & view all, but can't approve/reject
  - ✅ Equipment: View list (read-only, no edit/delete)
  - ✅ Reports: Can create and view report requests
  - ❌ Cannot access Users page
  - ❌ Cannot approve reservations
- **Test Actions**:
  1. Login → See full dashboard
  2. Equipment page → No edit/delete buttons visible
  3. Reservations → "Aprobar" and "Rechazar" buttons hidden
  4. Reports → Can access and create requests

### 3. **Encargado de Laboratorio (Lab Manager)**
- **Email**: encargado@test.com
- **Expected Access**:
  - ✅ Dashboard (full view)
  - ✅ Equipment: Full CRUD (create, edit, delete)
  - ✅ Reservations: View all and approve/reject
  - ✅ Can manage equipment status
  - ❌ Cannot access Users page
  - ❌ Cannot create/manage reports
- **Test Actions**:
  1. Login → See full dashboard items
  2. Equipment page → "Nuevo Equipo", Edit, Delete buttons visible
  3. Reservations → Can approve/reject pending reservations
  4. Try accessing `/users` → Should be blocked
  5. Try accessing Reports → Should be blocked/limited

### 4. **Administrador (Admin)**
- **Email**: admin@test.com
- **Expected Access**:
  - ✅ All pages accessible
  - ✅ Users: Full CRUD management
  - ✅ Equipment: Full CRUD
  - ✅ Reservations: View & approve/reject
  - ✅ Reports: Create, process, complete, delete
  - ✅ Dashboard: All stats visible
  - ❌ Super Admin functions (role assignment restrictions)
- **Test Actions**:
  1. Login → See complete dashboard
  2. Users page → Can create, edit, delete users
  3. All action buttons visible across all pages
  4. Try accessing all modules → All accessible

### 5. **Super Admin**
- **Email**: superadmin@test.com
- **Expected Access**:
  - ✅ Unrestricted access to ALL modules
  - ✅ Everything Admin has + potentially restricted settings
  - ✅ All CRUD operations
- **Test Actions**:
  1. Login → See complete dashboard
  2. All pages fully accessible
  3. All action buttons visible
  4. Can perform all operations

---

## RBAC Testing Checklist

### Route Protection Tests
- [ ] Unauthenticated user redirected to `/login`
- [ ] Estudiante accessing `/users` → Access Denied message
- [ ] Docente accessing `/users` → Access Denied message
- [ ] Encargado accessing `/users` → Access Denied message
- [ ] Admin accessing `/users` → Full access
- [ ] Docente accessing reports → Limited access (view/create only)

### Component Permission Tests

#### Reservations Page
- [ ] Estudiante: "Nueva Reserva" button visible
- [ ] Docente: "Nueva Reserva" visible, "Aprobar"/"Rechazar" hidden
- [ ] Encargado: All buttons visible
- [ ] Admin: All buttons visible
- [ ] Approve/Reject buttons disabled for unauthorized users

#### Equipment Page
- [ ] Estudiante: "Nuevo Equipo" button NOT visible
- [ ] Docente: No Edit/Delete buttons
- [ ] Encargado: All buttons visible (Nuevo, Edit, Delete)
- [ ] Admin: All buttons visible

#### Users Page
- [ ] Docente accessing users page → Access Denied
- [ ] Estudiante accessing users page → Access Denied
- [ ] Admin: "Nuevo Usuario" visible, Edit/Delete buttons visible
- [ ] Can create, edit, delete users

#### Reports Page
- [ ] Docente: "Nueva Solicitud" button visible
- [ ] Encargado: Reports page restricted/blocked
- [ ] Admin: Full access to all report functions
- [ ] Action buttons (Procesar, Rechazar, Completar) shown only for authorized roles

#### Dashboard Page
- [ ] Estudiante: Only "Nueva Reserva" quick link visible
- [ ] Docente: "Nueva Reserva", "Ver Equipos", "Reportes" visible
- [ ] Encargado: All quick links visible except Users
- [ ] Admin: All quick links visible
- [ ] Equipment/Reservations stat sections visible per role

### Backend API Tests

#### Via Browser DevTools Network Tab

1. **Create Reservation as Estudiante**
   - POST `/api/reservations`
   - Should succeed
   - Check only own reservations returned

2. **Try to Approve Reservation as Estudiante**
   - POST `/api/reservations/{id}/approve`
   - Should return **403 Forbidden**

3. **Try to Access Users as Docente**
   - GET `/api/users`
   - Should return **403 Forbidden**

4. **Create Equipment as Admin**
   - POST `/api/equipment`
   - Should succeed

5. **Try to Edit Equipment as Docente**
   - PUT `/api/equipment/{id}`
   - Should return **403 Forbidden**

---

## Expected Permission Matrix

| Action | Super Admin | Admin | Lab Manager | Teacher | Student |
|--------|-------------|-------|-------------|---------|---------|
| View Users | ✅ | ✅ | ❌ | ❌ | ❌ |
| Create User | ✅ | ✅ | ❌ | ❌ | ❌ |
| Edit User | ✅ | ✅ | ❌ | ❌ | ❌ |
| Delete User | ✅ | ✅ | ❌ | ❌ | ❌ |
| View Equipment | ✅ | ✅ | ✅ | ✅ | ✅ |
| Create Equipment | ✅ | ✅ | ✅ | ❌ | ❌ |
| Edit Equipment | ✅ | ✅ | ✅ | ❌ | ❌ |
| Delete Equipment | ✅ | ✅ | ✅ | ❌ | ❌ |
| View All Reservations | ✅ | ✅ | ✅ | ✅ | ❌ |
| Create Reservation | ✅ | ✅ | ✅ | ✅ | ✅ |
| View Own Reservations | ✅ | ✅ | ✅ | ✅ | ✅ |
| Approve Reservation | ✅ | ✅ | ✅ | ❌ | ❌ |
| Reject Reservation | ✅ | ✅ | ✅ | ❌ | ❌ |
| Cancel Own Reservation | ✅ | ✅ | ✅ | ✅ | ✅ |
| View Reports | ✅ | ✅ | ❌ | ✅ | ❌ |
| Create Report Request | ✅ | ✅ | ❌ | ✅ | ❌ |
| Process Report Request | ✅ | ✅ | ❌ | ❌ | ❌ |
| Download Report | ✅ | ✅ | ❌ | ✅ | ❌ |

---

## Step-by-Step Testing Flow

### Test 1: Student Cannot Approve Reservations
1. Login as **Estudiante**
2. Go to Reservations page
3. Verify: "Aprobar" and "Rechazar" buttons should NOT be visible
4. Open browser DevTools → Network tab
5. Try to approve via API: `POST http://localhost:8000/api/reservations/{id}/approve`
6. Expected: 403 Forbidden error

### Test 2: Teacher Cannot Access Users
1. Login as **Docente**
2. Try to navigate to `/users`
3. Expected: Access Denied message and redirect
4. Try API: `GET http://localhost:8000/api/users`
5. Expected: 403 Forbidden error

### Test 3: Lab Manager Can Approve Reservations
1. Login as **Encargado de Laboratorio**
2. Go to Reservations page
3. Verify: "Aprobar" and "Rechazar" buttons ARE visible
4. Create a test reservation as Estudiante
5. Login as Encargado, approve it
6. Switch to Estudiante, see status changed to "Aprobada"

### Test 4: Admin Full Access
1. Login as **Administrador**
2. Verify all pages accessible
3. Can see all quick access links on Dashboard
4. Can create user → Go to Users page → "+ Nuevo Usuario"
5. Can manage equipment → Go to Equipment → Create/Edit/Delete
6. Can approve reservations

---

## Troubleshooting

### Issue: Frontend shows "Not Found" for protected pages
- **Solution**: Make sure you're authenticated first
- Check that token is stored in localStorage
- Verify backend is running on port 8000

### Issue: Buttons are grayed out but not hidden
- **Solution**: This is expected for some permission checks
- ProtectedButton shows a visual indication (opacity-50, disabled state)
- User cannot click the button

### Issue: 403 errors in Network tab
- **Solution**: This is CORRECT behavior
- Means your permission check is working
- Backend is properly blocking unauthorized requests

### Issue: Backend returns 500 error
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database migration ran: `php artisan migrate`
- Check middleware registration: `bootstrap/app.php`

---

## Key Files for RBAC System

### Backend
- `routes/api.php` - Permission middleware on all routes
- `app/Http/Middleware/CheckPermission.php` - Permission validation logic
- `app/Http/Controllers/Api/ReservationController.php` - Business logic with role filtering
- `bootstrap/app.php` - Middleware registration

### Frontend
- `src/components/ProtectedRoute.tsx` - Route-level protection
- `src/components/ProtectedFeature.tsx` - Component-level protection
- `src/hooks/usePermissions.ts` - Permission matrix and checks
- `src/App.tsx` - Routes wrapped with ProtectedRoute
- `src/pages/*.tsx` - Pages using IfCan/ProtectedButton

---

## Test Results Log

```
Date: __________
Tester: __________

Test Results:
- Route Protection: PASS / FAIL
- Component Permission: PASS / FAIL
- Backend API Validation: PASS / FAIL
- Role Hierarchy: PASS / FAIL
- Dashboard Visibility: PASS / FAIL

Issues Found:
- ________________
- ________________

Notes:
________________
```

---

## Next Steps

1. ✅ Complete all test scenarios above
2. ✅ Document any issues or edge cases
3. ✅ Verify role permissions match requirements
4. ✅ Check UI/UX for permission-denied messages
5. ✅ Test on different browsers
6. ✅ Load test with multiple concurrent users (optional)

---

## Support

If you encounter issues:
1. Check the browser console for errors
2. Check Network tab for 403/401 responses
3. Check backend logs: `storage/logs/laravel.log`
4. Verify `.env` database configuration
5. Confirm all users exist in database

**Happy Testing! 🚀**
