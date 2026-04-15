# RBAC Developer Reference

Quick reference for working with the Role-Based Access Control system in Lab Nova.

---

## Quick Links

- 📖 [Full Implementation Summary](./RBAC_IMPLEMENTATION_SUMMARY.md)
- 🧪 [Testing Guide](./TESTING_RBAC_GUIDE.md)
- 👤 [Credentials & Users](./Credenciales.txt)

---

## Frontend Usage

### Protecting a Route

```tsx
// App.tsx
import { ProtectedRoute } from './components/ProtectedRoute'

<Route 
  path="/users" 
  element={
    <ProtectedRoute requiredModule="users">
      <Users />
    </ProtectedRoute>
  } 
/>
```

### Protecting a Button

```tsx
import { ProtectedButton } from './components/ProtectedFeature'

<ProtectedButton 
  permission={{ module: 'equipment', action: 'edit' }}
  onClick={handleEdit}
  className="text-blue-600"
>
  Editar
</ProtectedButton>
```

### Conditional Rendering

```tsx
import { IfCan } from './components/ProtectedFeature'

<IfCan permission={{ module: 'users', action: 'create' }}>
  <button onClick={handleCreate}>
    + Nuevo Usuario
  </button>
</IfCan>
```

### Checking Permissions in Code

```tsx
import { usePermissions } from '../hooks/usePermissions'

const MyComponent = () => {
  const perms = usePermissions()
  
  // Generic check
  if (perms.can('reservations', 'edit')) {
    // Show approval UI
  }
  
  // Specific checks
  if (perms.canApproveReservation()) { }
  if (perms.canManageUsers()) { }
  if (perms.isSuperAdmin()) { }
  if (perms.isStudent()) { }
}
```

### Available Permission Methods

```typescript
// Generic
can(module: string, action: string): boolean
canView/Create/Edit/Delete(module: string): boolean

// Specific operations
canApproveReservation(): boolean
canManageUsers(): boolean
canManageEquipment(): boolean
canCreateReport(): boolean

// Role helpers
isSuperAdmin(): boolean
isAdmin(): boolean
isLabManager(): boolean
isTeacher(): boolean
isStudent(): boolean

// Role name
userRole: string
```

---

## Backend Usage

### Protecting an API Route

```php
// routes/api.php
Route::post('/reservations/{id}/approve', [ReservationController::class, 'approve'])
    ->middleware('permission:reservations,edit');

Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->middleware('permission:users,delete');
```

### Manual Permission Check in Controller

```php
// app/Http/Controllers/Api/SomeController.php

public function someAction(Request $request)
{
    $user = $request->user();
    
    // Check if user has permission
    if (!$user->can('module', 'action')) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    
    // Continue with action
}
```

### Role-Based Data Filtering

```php
public function index(Request $request)
{
    $user = $request->user();
    
    if ($user->role->name === 'Estudiante') {
        // Students see only their own
        $data = Model::where('user_id', $user->id)->get();
    } else {
        // Others see all
        $data = Model::all();
    }
    
    return response()->json($data);
}
```

---

## Permission Matrix Reference

### Modules
- `users` - User management
- `equipment` - Equipment management
- `reservations` - Reservations
- `reports` - Reports
- `dashboard` - Dashboard

### Actions
- `view` - Read access
- `create` - Create new items
- `edit` - Modify existing items
- `delete` - Delete items

### Permission Format
```typescript
permission={{ module: 'reservations', action: 'edit' }}
```

---

## Adding New Permissions

### Step 1: Frontend Permission Hook
```typescript
// src/hooks/usePermissions.ts
const rolePermissions = {
  'Estudiante': { 
    newModule: ['view'] 
  },
  'Docente': { 
    newModule: ['view', 'create'] 
  },
  'Encargado de Laboratorio': { 
    newModule: ['view', 'create', 'edit'] 
  },
  'Administrador': { 
    newModule: ['view', 'create', 'edit', 'delete'] 
  },
  'Super Admin': { 
    newModule: ['view', 'create', 'edit', 'delete'] 
  }
}
```

### Step 2: Backend Middleware
```php
// routes/api.php
Route::post('/newmodule', [NewModuleController::class, 'store'])
    ->middleware('permission:newModule,create');
```

### Step 3: Protect UI Component
```tsx
<ProtectedButton 
  permission={{ module: 'newModule', action: 'create' }}
  onClick={handleCreate}
>
  + Nuevo
</ProtectedButton>
```

### Step 4: Update Database (if needed)
```php
// database/seeders/SetupSeeder.php
// Add role-permission mappings
```

---

## Common Patterns

### Pattern 1: Admin Only Section
```tsx
<IfCan permission={{ module: 'users', action: 'view' }}>
  <AdminPanel />
</IfCan>
```

### Pattern 2: Edit with Permission Check
```tsx
{perms.can('equipment', 'edit') && (
  <button onClick={handleEdit}>Edit</button>
)}
```

### Pattern 3: Dependent Actions
```tsx
<ProtectedButton 
  permission={{ module: 'reservations', action: 'edit' }}
  onClick={handleApprove}
  disabled={actionLoading}
>
  {actionLoading ? 'Aprobando...' : 'Aprobar'}
</ProtectedButton>
```

### Pattern 4: Role-Specific UI
```tsx
export const RoleSpecificView = () => {
  const perms = usePermissions()
  
  if (perms.isStudent()) {
    return <StudentView />
  } else if (perms.isTeacher()) {
    return <TeacherView />
  } else if (perms.isAdmin()) {
    return <AdminView />
  }
}
```

---

## Debugging

### Check Your Permissions
```tsx
const perms = usePermissions()
console.log(perms.userRole) // Current role
console.log(perms.can('users', 'view')) // Specific permission
```

### Check API Response
```
// In browser DevTools → Network tab
// Look for 403 Forbidden = Permission denied (correct)
// Look for 401 Unauthorized = Not authenticated
// Look for 200 OK = Allowed
```

### Check Backend Logs
```bash
tail -f storage/logs/laravel.log
```

### Debug Middleware
Add this in `CheckPermission.php`:
```php
\Log::info("Permission check: {$user->email} on {$module}.{$action}");
```

---

## Best Practices

✅ **DO**
- Always protect routes with `ProtectedRoute`
- Use `IfCan` to hide unauthorized UI
- Validate permissions on backend
- Use specific permission checks
- Document permission requirements
- Test with multiple roles

❌ **DON'T**
- Rely only on frontend permission checks
- Leave API routes unprotected
- Show error messages that reveal system info
- Hard-code role logic in components
- Forget to test permission boundary cases
- Store permissions in localStorage

---

## Troubleshooting

**Q: Buttons are hidden but I expected them to show**
A: Check that your role has the permission in `usePermissions.ts` and that the permission is properly registered in the backend.

**Q: Getting 403 errors on everything**
A: Verify:
1. User is authenticated
2. User role exists and is assigned
3. Permission exists in database
4. Middleware is registered in `bootstrap/app.php`

**Q: Frontend doesn't match backend permissions**
A: Ensure:
1. Permission names match exactly (case-sensitive)
2. `usePermissions.ts` has same matrix as backend
3. Both frontend and backend are restarted

**Q: Can't figure out what permissions a role has**
A: Check `database/seeders/SetupSeeder.php` for role-permission mappings.

---

## Testing Permissions in DevTools

```javascript
// Browser Console - Get current permissions
const perms = usePermissions()
perms.can('users', 'view')
perms.isSuperAdmin()

// Or check API directly
fetch('http://localhost:8000/api/users')
  .then(r => console.log(r.status)) 
  // 200 = allowed, 403 = denied, 401 = not authenticated
```

---

## Related Files

- `src/components/ProtectedRoute.tsx` - Route protection
- `src/components/ProtectedFeature.tsx` - UI protection components
- `src/hooks/usePermissions.ts` - Permission logic (React hook)
- `app/Http/Middleware/CheckPermission.php` - Backend validation
- `routes/api.php` - Protected routes
- `database/seeders/SetupSeeder.php` - Roles and permissions

---

## Support

For more information:
1. Check the full [RBAC Implementation Summary](./RBAC_IMPLEMENTATION_SUMMARY.md)
2. Follow the [Testing Guide](./TESTING_RBAC_GUIDE.md)
3. Review the test user credentials in [Credenciales.txt](./Credenciales.txt)

