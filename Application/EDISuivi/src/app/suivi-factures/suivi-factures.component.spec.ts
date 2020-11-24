import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SuiviFacturesComponent } from './suivi-factures.component';

describe('SuiviFacturesComponent', () => {
  let component: SuiviFacturesComponent;
  let fixture: ComponentFixture<SuiviFacturesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SuiviFacturesComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SuiviFacturesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
