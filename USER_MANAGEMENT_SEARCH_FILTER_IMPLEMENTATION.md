# Auth Central - User Management Search & Filter Implementation

## Overview
Added comprehensive search and filter functionality to the Auth Central user management module, enabling efficient user lookup and management based on various criteria.

## Features Implemented

### 1. Search Functionality
- **Search by Name**: Real-time search through user names
- **Search by Email**: Find users by their email addresses
- **Debounced Input**: 300ms debounce to prevent excessive queries
- **Case-Insensitive**: Searches work regardless of text casing

### 2. Filter Functionality
- **Role Filter**: Filter users by their assigned role
- **Spatie Permission Integration**: Supports both column-based roles and Spatie Permission roles
- **Combined Filters**: Search and role filter can be used simultaneously
- **Clear Filters**: One-click button to reset all filters

### 3. Pagination
- **Paginated Results**: 15 users per page (configurable via `$perPage` property)
- **Smart Reset**: Pagination automatically resets when filters change
- **Result Count**: Shows "Showing X to Y of Z results"
- **Bootstrap Theme**: Uses Bootstrap pagination styling

### 4. User Experience Enhancements
- **Loading Indicator**: Shows spinner while filtering/searching
- **Empty State**: User-friendly message when no results found
- **Active Filter Count**: Displays total user count in header
- **Query String Persistence**: Search and filters persist in URL
- **Confirmation Dialogs**: Confirm before deleting users
- **Role Badges**: Visual badges for both regular and Spatie roles
- **Icons**: Font Awesome icons for better visual clarity

## Files Modified

### 1. `app/Livewire/UserManagement.php`
**Changes:**
- Added `WithPagination` trait
- Added search and filter properties (`$search`, `$roleFilter`, `$perPage`)
- Implemented `getUsers()` method with query building logic
- Added `clearFilters()` method
- Added pagination lifecycle hooks (`updatingSearch`, `updatingRoleFilter`)
- Enhanced role loading with `loadAvailableRoles()` method
- Improved error handling with detailed error messages
- Added Spatie Permission role syncing in `save()` method
- Removed `fetchUsers()` method (replaced with paginated `getUsers()`)

### 2. `resources/views/livewire/user-management.blade.php`
**Changes:**
- Added search input with Font Awesome icon
- Added role filter dropdown
- Added "Clear Filters" button
- Added loading indicator with `wire:loading`
- Added user count display in header
- Enhanced table with role badges (primary for column role, secondary for Spatie roles)
- Added pagination links
- Added "Showing X to Y of Z" results counter
- Added empty state with conditional messaging
- Added confirmation dialog for delete action
- Enhanced buttons with icons
- Changed from `@foreach` to `@forelse` for better empty state handling

### 3. `tests/Feature/UserManagementTest.php` (NEW)
**Test Coverage:**
- Search by name
- Search by email
- Filter by role column
- Filter by Spatie Permission role
- Combined search and filter
- Clear filters functionality
- Pagination reset on search
- Case-insensitive search
- Empty state display
- User count updates

## Technical Details

### Query Optimization
The `getUsers()` method uses efficient query building:
```php
public function getUsers()
{
    $query = User::query();

    // Search by name or email
    if ($this->search) {
        $query->where(function($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('email', 'like', '%' . $this->search . '%');
        });
    }

    // Filter by role (checking both role column and Spatie roles)
    if ($this->roleFilter) {
        $query->where(function($q) {
            $q->where('role', $this->roleFilter)
              ->orWhereHas('roles', function($roleQuery) {
                  $roleQuery->where('name', $this->roleFilter);
              });
        });
    }

    return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
}
```

### URL Query String Persistence
Filters persist in URL for bookmarking and sharing:
```php
protected $queryString = [
    'search' => ['except' => ''],
    'roleFilter' => ['except' => ''],
];
```

### Role Display Logic
Shows both column-based and Spatie Permission roles:
```blade
@if($user->role)
    <span class="badge badge-primary">{{ ucfirst($user->role) }}</span>
@endif
@if(method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty())
    @foreach($user->getRoleNames() as $roleName)
        <span class="badge badge-secondary ml-1">{{ ucfirst($roleName) }}</span>
    @endforeach
@endif
```

## Configuration

### Adjustable Settings
To modify the behavior, update these properties in `UserManagement.php`:

```php
public $perPage = 15;  // Change items per page
```

To modify debounce timing in the view:
```blade
wire:model.live.debounce.300ms="search"  // Change 300ms to desired value
```

## Testing

Run the test suite:
```bash
cd authcentral
php artisan test --filter UserManagementTest
```

## Usage

### For End Users
1. Navigate to User Management page
2. Use the search box to find users by name or email
3. Use the role dropdown to filter by specific roles
4. Click "Clear Filters" to reset all filters
5. Navigate through pages using pagination controls

### For Developers
The implementation follows Laravel/Livewire best practices:
- Uses Livewire's reactive properties
- Implements proper pagination
- Follows single responsibility principle
- Includes comprehensive test coverage
- Uses query string for state persistence

## Future Enhancements
Potential improvements:
- Export filtered results to CSV
- Advanced filters (date range, status, etc.)
- Bulk actions on filtered users
- Saved filter presets
- Column sorting
- Customizable columns display

## Dependencies
- Laravel Livewire 3.x
- Spatie Laravel Permission
- Bootstrap 4/5
- Font Awesome (for icons)

## Notes
- The implementation supports both `role` column and Spatie Permission roles
- Pagination automatically resets when filters change
- All searches are case-insensitive
- Query strings allow direct linking to filtered views
