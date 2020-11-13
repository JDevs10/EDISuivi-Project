import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SuiviCommandeDetailComponent } from './suivi-commande-detail.component';

describe('SuiviCommandeDetailComponent', () => {
  let component: SuiviCommandeDetailComponent;
  let fixture: ComponentFixture<SuiviCommandeDetailComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SuiviCommandeDetailComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SuiviCommandeDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
